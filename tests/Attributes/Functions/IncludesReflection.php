<?php

namespace Crell\AttributeUtils\Attributes\Functions;

use Crell\AttributeUtils\FromReflectionFunction;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class IncludesReflection implements FromReflectionFunction
{
    public readonly string $name;

    public function fromReflection(\ReflectionFunction $subject): void
    {
        $this->name = $subject->name;
    }
}
