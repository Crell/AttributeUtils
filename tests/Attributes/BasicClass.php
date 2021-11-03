<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Inheritable;

#[Attribute(Attribute::TARGET_CLASS)]
class BasicClass implements Inheritable
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}
}
