<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ClassSubAttribute
{
    public function __construct(
        public string $c = 'C',
    ) {}
}
