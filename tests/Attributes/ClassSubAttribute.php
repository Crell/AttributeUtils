<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Inheritable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ClassSubAttribute implements Inheritable
{
    public function __construct(
        public string $c = 'C',
    ) {}
}
