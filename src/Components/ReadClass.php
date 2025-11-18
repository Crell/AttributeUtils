<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Components;

class ReadClass implements Component
{
    public function __construct(
        public readonly \Closure $callback,
    ) {}
}
