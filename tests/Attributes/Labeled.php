<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Crell\AttributeUtils\ParseProperties;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Labeled implements ParseProperties
{
    public readonly array $properties;

    public function setProperties(array $properties): void
    {
        $this->properties ??= $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    public function propertyAttribute(): string
    {
        return Label::class;
    }
}
