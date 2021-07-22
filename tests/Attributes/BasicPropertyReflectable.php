<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;
use Crell\ObjectAnalyzer\FromReflectionProperty;

/**
 * A basic property attribute.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class BasicPropertyReflectable implements FromReflectionProperty
{
    public function __construct(
        public string $a = 'a',
        public string $b = 'b',
        public ?string $name = null,
    ) {}

    public function fromReflection(\ReflectionProperty $subject): void
    {
        $this->name ??= $subject->getName();
    }
}
