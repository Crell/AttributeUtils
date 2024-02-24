<?php

namespace Crell\AttributeUtils\Attributes\Functions;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class RequiredArg
{
    public function __construct(public readonly string $a) {}
}
