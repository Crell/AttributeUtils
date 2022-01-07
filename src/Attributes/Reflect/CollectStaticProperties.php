<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectStaticProperties
{
    /** @var ReflectProperty[] */
    public readonly array $staticProperties;

    public function setStaticProperties(array $properties): void
    {
        $this->staticProperties = $properties;
    }

    public function includeStaticPropertiesByDefault(): bool
    {
        return true;
    }

    abstract public function staticPropertyAttribute(): string;
}
