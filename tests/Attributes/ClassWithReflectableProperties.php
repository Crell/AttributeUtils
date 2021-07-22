<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\ParseProperties;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithReflectableProperties implements ParseProperties
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
        return PropertyWithReflection::class;
    }
}
