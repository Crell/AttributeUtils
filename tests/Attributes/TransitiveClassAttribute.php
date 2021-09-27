<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseProperties;

// This attribute is not transtiive, but it's a holder
// for a field attribute that is.
#[Attribute(Attribute::TARGET_CLASS)]
class TransitiveClassAttribute implements ParseProperties
{
    public array $properties;

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includeByDefault(): bool
    {
        return true;
    }

    public static function propertyAttribute(): string
    {
        return TransitivePropertyAttribute::class;
    }

}
