<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HasRequiredArgs
{
    public function __construct(
        public int $x,
    ) {}
}
