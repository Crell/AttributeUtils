<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\Fieldable;

/**
 * A simple class attribute that includes support for fields/properties.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassFielded implements Fieldable
{
    public array $fields = [];

    public function __construct(
        public int $a = 0,
        public int $b = 0,
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
        return BasicProperty::class;
    }

}
