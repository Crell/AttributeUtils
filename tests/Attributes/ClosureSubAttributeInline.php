<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

#[\Attribute]
class ClosureSubAttributeInline
{
    public function __construct(public string $b) {}
}
