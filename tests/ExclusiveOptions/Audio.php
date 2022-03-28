<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\ExclusiveOptions;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Audio implements DisplayType
{
    public function __construct(public readonly int $volume) {}
}
