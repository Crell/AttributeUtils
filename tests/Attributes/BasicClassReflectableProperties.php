<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\Fieldable;

/**
 * Includes fields that are themselves reflectable.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassReflectableProperties implements Fieldable
{
    public array $fields = [];

    public function __construct(
        public bool $include = true,
    ) {}

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
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
