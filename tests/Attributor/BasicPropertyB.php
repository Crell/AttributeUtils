<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributor;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class BasicPropertyB
{
    public function __construct(
        public string $b = 'B',
    ) {}
}
