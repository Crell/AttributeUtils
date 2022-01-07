<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectProperties
{
    /** @var ReflectProperty[] */
    public readonly array $properties;

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    abstract public function propertyAttribute(): string;
}
