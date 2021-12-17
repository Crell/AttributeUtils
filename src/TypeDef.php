<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\all;
use function Crell\fp\any;

class TypeDef
{
    // Normalized to DNF form. (ORed list of ANDs.)
    private array $type = [[]];

    public readonly bool $allowsNull;

    public readonly TypeComplexity $complexity;

    /**
     * @todo Unclear if self and static should resolve to their actual classes. Right now they do not.
     *
     * @param \ReflectionType $type
     */
    public function __construct(\ReflectionType $type)
    {
        $this->allowsNull = $type->allowsNull();

        $this->type = match ($type::class) {
            \ReflectionNamedType::class => [[$type->getName()]],
            \ReflectionUnionType::class => $this->parseUnionType($type),
            \ReflectionIntersectionType::class => [$this->parseIntersectionType($type)],
        };

        $this->complexity = $this->deriveComplexity($this->type);
    }

    public function isSimple(): bool
    {
        return $this->complexity === TypeComplexity::Simple;
    }

    // @todo What should this do if it's not a simple type?
    public function getSimpleType(): string
    {
        return $this->type[0][0];
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

        $typeAccepts = fn ($matchType): bool
            => (class_exists($matchType) || interface_exists($matchType))
                ? is_a($type, $matchType, true)
                : $type === $matchType;

        $intersectionAccepts = fn (array $segment): bool  => all($typeAccepts)($segment);

        return any($intersectionAccepts)($this->type);
    }

    protected function parseUnionType(\ReflectionUnionType $type): array
    {
        $translate = static fn (\ReflectionType $innerType): array => match($innerType::class) {
            \ReflectionNamedType::class => [$innerType->getName()],
            // This technically cannot happen until 8.2, assuming we get DNF types, but planning ahead...
            \ReflectionIntersectionType::class => $this->parseIntersectionType($innerType),
        };
        return array_map($translate, $type->getTypes());
    }

    protected function parseIntersectionType(\ReflectionIntersectionType $type): array
    {
        $translate = static fn (\ReflectionNamedType $innerType): string => $innerType->getName();
        return array_map($translate, $type->getTypes());
    }


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
