<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\FromReflectionMethod;

#[Attribute(Attribute::TARGET_METHOD)]
class MethodWithReflection implements FromReflectionMethod
{
    public function __construct(
        public string $a = 'a',
        public string $b = 'b',
        public ?string $name = null,
    ) {}

    public function fromReflection(\ReflectionMethod $subject): void
    {
        $this->name ??= $subject->getName();
    }
}
