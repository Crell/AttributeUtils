<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Excludable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BasicExcludableProperty implements Excludable
{
    public function __construct(protected bool $exclude = false) {}

    public function exclude(): bool
    {
        return $this->exclude;
    }
}
