<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

interface ParseProperties
{
    public function setProperties(array $properties): void;

    public function includeByDefault(): bool;

    public static function propertyAttribute(): string;
}
