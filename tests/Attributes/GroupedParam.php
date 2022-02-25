<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\SupportsGroups;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
class GroupedParam implements SupportsGroups
{
    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}

    public function groups(): array
    {
        return $this->group ? [$this->group] : [];
    }
}
