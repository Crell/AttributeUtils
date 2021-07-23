<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\FromReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyWithReflection implements FromReflectionProperty
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
