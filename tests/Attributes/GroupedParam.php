<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
class GroupedParam
{
    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}
}
