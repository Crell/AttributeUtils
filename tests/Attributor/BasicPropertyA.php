<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributor;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class BasicPropertyA
{
    public function __construct(
        public string $a = 'A',
    ) {}
}
