<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class PropertyTakesClassDefaultClass implements ParseProperties
{
    /** @var PropertyTakesClassDefault[] */
    public array $properties;

    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}

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
        return PropertyTakesClassDefault::class;
    }


}
