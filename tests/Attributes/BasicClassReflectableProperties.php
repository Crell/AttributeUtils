<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\ParseProperties;

/**
 * Includes fields that are themselves reflectable.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassReflectableProperties implements ParseProperties
{
    public array $properties = [];

    public function __construct(
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
        return BasicPropertyReflectable::class;
    }
}
