<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\all;
use function Crell\fp\any;
use function Crell\fp\method;

class TypeDef
{
    /**
     * Normalized to DNF form. (ORed list of ANDs.)
     *
     * @var array<array<string>>
     */
    private array $type = [[]];

    public readonly bool $allowsNull;

    public readonly TypeComplexity $complexity;

    /**
     * @todo Unclear if self and static should resolve to their actual classes. Right now they do not.
     *
     * @param ?\ReflectionType $type
     */
    public function __construct(?\ReflectionType $type)
    {
        if (is_null($type)) {
            $this->allowsNull = true;
            $this->type = [['mixed']];
            $this->complexity = TypeComplexity::Simple;
            return;
        }

        // PHPStan thinks this property is already assigned, despite
        // the return statement above. This is a bug in PHPStan.
        // @phpstan-ignore-next-line
        $this->allowsNull = $type->allowsNull();

        // PHPStan thinks this property is already assigned, despite
        // the return statement above. This is a bug in PHPStan.
        // @phpstan-ignore-next-line
        $this->type = match ($type::class) {
            \ReflectionNamedType::class => [[$type->getName()]],
            \ReflectionUnionType::class => $this->parseUnionType($type),
            \ReflectionIntersectionType::class => [$this->parseIntersectionType($type)],
        };

        // PHPStan thinks this property is already assigned, despite
        // the return statement above. This is a bug in PHPStan.
        // @phpstan-ignore-next-line
        $this->complexity = $this->deriveComplexity($this->type);
    }

    public function isSimple(): bool
    {
        return $this->complexity === TypeComplexity::Simple;
    }

    /**
     * Returns the simple type for this definition, or null if it's not simple.
     *
     * @return string|null
     *   The simple type as a string, or null if it's not a simple type.
     */
    public function getSimpleType(): ?string
    {
        return $this->isSimple() ? $this->type[0][0] : null;
    }

    /**
     * Determines if this type definition will accept a value of the specified type.
     *
     * @todo Not sure what to do with static, self, etc.
     *
     * @param string $type
     *   A simple string type, like "int", "float", "SomeClass", etc.
     *   Classes should include their full namespace.
     * @return bool
     */
    public function accepts(string $type): bool
    {
        if ($type === 'null') {
            return $this->allowsNull;
        }

        $typeAccepts = fn ($defType): bool => match(true) {
                $defType === 'mixed' => true,
                class_exists($defType) || interface_exists($defType) => is_a($type, $defType, true),
                default => $type === $defType,
            };

        $intersectionAccepts = fn (array $segment): bool  => all($typeAccepts)($segment);

        return any($intersectionAccepts)($this->type);
    }

    /**
     * @return array<array<int, string>>
     */
    protected function parseUnionType(\ReflectionUnionType $type): array
    {
        $translate = static fn (\ReflectionType $innerType): array => match($innerType::class) {
            \ReflectionNamedType::class => [$innerType->getName()],
            // This technically cannot happen until 8.2, assuming we get DNF types, but planning ahead...
            //\ReflectionIntersectionType::class => $this->parseIntersectionType($innerType),
        };
        return array_map($translate, $type->getTypes());
    }

    /**
     * @return array<array<int, string>>
     */
    protected function parseIntersectionType(\ReflectionIntersectionType $type): array
    {
        return array_map(method('getName'), $type->getTypes());
    }

    /**
     *
     *
     * @param array<string>|array<array<string>> $type
     */
    protected function deriveComplexity(array $type): TypeComplexity
    {
        if (count($type) === 1 && count($type[0]) === 1) {
            return TypeComplexity::Simple;
        }

        if (count($type) > 1) {
            // It's either a union or a compound.
            return any(static fn (array $s):bool => count($s) > 1)($type)
                ? TypeComplexity::Compound
                : TypeComplexity::Union;
        }

        return TypeComplexity::Intersection;
    }
}
