<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\TypeDef;

class ParentClass
{
    public function returnsSelfParent(): self
    {
    }

    public function returnsStatic(): static
    {
    }
}
