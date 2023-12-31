<?php

namespace Crell\AttributeUtils\Attributes\Functions;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class SubChild
{
    public function __construct(public string $b = 'default') {}
}
