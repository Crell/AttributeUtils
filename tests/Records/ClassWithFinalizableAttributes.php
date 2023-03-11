<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\FinalizableClassAttribute;
use Crell\AttributeUtils\Attributes\FinalizablePropertyAttribute;

#[FinalizableClassAttribute]
class ClassWithFinalizableAttributes
{
    public function __construct(
        #[FinalizablePropertyAttribute(a: 5, b: 2)]
        public string $foo = 'bar',
    ) {}
}
