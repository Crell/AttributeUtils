<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

interface Fieldable
{
    public function setFields(array $fields): void;

    public function includeProperties(): array;

    public function excludeProperties(): array;

    public function includeByDefault(): bool;

    public static function propertyAttribute(): string;
}
