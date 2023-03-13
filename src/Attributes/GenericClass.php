<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseProperties;

/**
 * Generic class for parsing properties.
 *
 * This class attribute has no data of its own. All it's good for
 * is specifying how to parse properties in the class. You can use
 * this attribute if you just care about the properties but don't want
 * to bother making your own class-level attribute.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class GenericClass implements ParseProperties
{
    /**
     * @var array<string, object>
     */
    public array $properties;

    public function __construct(
        protected string $propertyAttribute,
        protected bool $includeByDefault = true,
    ) {}

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return $this->includeByDefault;
    }

    public function propertyAttribute(): string
    {
        return $this->propertyAttribute;
    }
}
