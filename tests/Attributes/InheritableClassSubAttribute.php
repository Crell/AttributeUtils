<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Inheritable;

#[Attribute(Attribute::TARGET_CLASS)]
class InheritableClassSubAttribute implements Inheritable
{
    public function __construct(
        public string $foo = 'bar',
    ) {}
}
