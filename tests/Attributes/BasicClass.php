<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class BasicClass
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}
}
