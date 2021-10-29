<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Crell\AttributeUtils\ParseProperties;

#[\Attribute(\Attribute::TARGET_CLASS)]
class GenericPropertyHolder implements ParseProperties
{
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
