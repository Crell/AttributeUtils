<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\ParseProperties;

/**
 * A simple class attribute that includes support for fields/properties.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassFielded implements ParseProperties
{
    public array $properties = [];

    public function __construct(
        public int $a = 0,
        public int $b = 0,
        public bool $include = true,
    ) {}

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includeByDefault(): bool
    {
        return $this->include;
    }

    public static function propertyAttribute(): string
    {
        return BasicProperty::class;
    }
}
