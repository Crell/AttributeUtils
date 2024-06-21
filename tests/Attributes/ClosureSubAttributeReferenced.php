<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

#[\Attribute]
class ClosureSubAttributeReferenced
{
    public function __construct(public string $a) {}
}
