<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Inheritable;

#[Attribute(Attribute::TARGET_CLASS)]
class InheritablePropertySubAttribute implements Inheritable
{
    public function __construct(
        public string $narf = 'poink',
    ) {}
}
