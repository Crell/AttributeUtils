<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class ConfigurableClassWithProperties implements ParseProperties
{
    /**
     * @var array<string, ?object>
     */
    public array $properties = [];

    public function __construct(
        public string $propertyAttribute,
        public bool $includeByDefault = false,
        public string $a = 'A',
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
