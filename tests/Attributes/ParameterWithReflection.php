<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\FromReflectionParameter;

#[Attribute(Attribute::TARGET_PARAMETER)]
class ParameterWithReflection implements FromReflectionParameter
{
    public function __construct(
        public int $x = 1,
        public int $y = 2,
        public ?string $name = null,
    ) {}

    public function fromReflection(\ReflectionParameter $subject): void
    {
        $this->name ??= $subject->getName();
    }
}
