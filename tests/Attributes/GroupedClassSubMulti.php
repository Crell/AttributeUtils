<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class GroupedClassSubMulti
{
    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}
}
