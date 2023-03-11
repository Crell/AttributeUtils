<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Finalizable;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class FinalizableClassAttribute implements Finalizable, ParseProperties
{
    public readonly bool $wasFinalized;

    public readonly array $properties;

    public function finalize(): void
    {
        $this->wasFinalized = true;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    public function propertyAttribute(): string
    {
        return FinalizablePropertyAttribute::class;
    }
}
