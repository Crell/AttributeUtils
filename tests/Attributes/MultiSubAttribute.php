<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class MultiSubAttribute
{
    public function __construct(
        public string $name,
    ) {}
}
