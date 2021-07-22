<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

interface Fieldable
{
    public function setFields(array $fields): void;

    public function includeByDefault(): bool;

    public static function propertyAttribute(): string;
}
