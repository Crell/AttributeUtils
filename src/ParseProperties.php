<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface ParseProperties
{
    public function setProperties(array $properties): void;

    public function includeByDefault(): bool;

    public static function propertyAttribute(): string;
}
