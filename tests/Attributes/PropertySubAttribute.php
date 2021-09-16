<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertySubAttribute
{
    public function __construct(
        public string $b = 'B',
    ) {}
}
