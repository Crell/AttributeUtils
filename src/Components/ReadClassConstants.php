<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Components;

class ReadClassConstants implements Component
{
    public function __construct(
        public readonly string $attribute,
        public readonly \Closure $callback,
        public readonly bool $includeByDefault = true,
    ) {}
}
