<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class GroupedMethod
{
    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}
}
