<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Components;

class ReadSubAttribute implements Component
{
    public function __construct(
        public readonly string $attribute,
        public readonly \Closure $callback,
    ) {}
}
