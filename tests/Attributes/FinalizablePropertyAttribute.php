<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Finalizable;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FinalizablePropertyAttribute implements Finalizable
{
    public readonly bool $greater;

    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}

    public function finalize(): void
    {
        $this->greater = $this->a > $this->b;
    }

}
