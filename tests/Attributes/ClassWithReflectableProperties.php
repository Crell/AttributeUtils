<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithReflectableProperties implements ParseProperties
{
    public array $properties = [];

    public function __construct(
        public bool $include = true,
    ) {}

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return $this->include;
    }

    public function propertyAttribute(): string
    {
        return PropertyWithReflection::class;
    }
}
