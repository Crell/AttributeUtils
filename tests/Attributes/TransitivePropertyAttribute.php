<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\TransitiveProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class TransitivePropertyAttribute implements TransitiveProperty
{
    public function __construct(
        public string $beep = 'default',
    ) {}
}
