<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

class ClassWithInterface implements AnInterface
{
    public function __construct(
        public int $a = 1,
    ) {}
}
