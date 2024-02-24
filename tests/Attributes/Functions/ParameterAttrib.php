<?php

namespace Crell\AttributeUtils\Attributes\Functions;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class ParameterAttrib
{
    public function __construct(public readonly string $a = 'default') {}
}
