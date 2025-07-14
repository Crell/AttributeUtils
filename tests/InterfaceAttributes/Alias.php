<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\InterfaceAttributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Alias implements Name
{
    public function __construct(public readonly string $name) {}

    public function fullName(): string
    {
        return $this->name;
    }
}
