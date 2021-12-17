<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\amap;
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
