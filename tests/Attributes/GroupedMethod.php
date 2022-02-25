<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectParameters;
use Crell\AttributeUtils\ParseParameters;
use Crell\AttributeUtils\SupportsGroups;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class GroupedMethod implements SupportsGroups, ParseParameters
{
    use CollectParameters;

    public function __construct(
        public string $val = 'Z',
        public ?string $group = null,
    ) {}

    public function groups(): array
    {
        return $this->group ? [$this->group] : [];
    }

    public function parameterAttribute(): string
    {
        return GroupedParam::class;
    }
}
