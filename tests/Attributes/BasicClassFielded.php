<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\Fieldable;

/**
 * The most basic class-level attribute. No fancy integration at all.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClassFielded implements Fieldable
{
    public array $fields = [];

    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function includeProperties(): array
    {
        // TODO: Implement includeProperties() method.
    }

    public function excludeProperties(): array
    {
        // TODO: Implement excludeProperties() method.
    }

    public function includeByDefault(): bool
    {
        // TODO: Implement includeByDefault() method.
    }

    public static function propertyAttribute(): string
    {
        return BasicProperty::class;
    }

}
