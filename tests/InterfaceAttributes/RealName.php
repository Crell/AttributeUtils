<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\InterfaceAttributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RealName implements Name
{
    public function __construct(
        public readonly string $first,
        public readonly string $last,
    ) {}

    public function fullName(): string
    {
        return "$this->first $this->last";
    }
}
