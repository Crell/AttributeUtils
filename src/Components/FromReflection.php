<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Components;

use Crell\AttributeUtils\Components\Component;

class FromReflection implements Component
{
    public function __construct(
        readonly public \Closure $callback,
    ) {}
}
