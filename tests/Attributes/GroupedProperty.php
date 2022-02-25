<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class GroupedProperty
{
    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}
}
