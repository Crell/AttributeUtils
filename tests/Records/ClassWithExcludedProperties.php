<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\BasicExcludableProperty;
use Crell\AttributeUtils\Attributes\GenericClass;

#[GenericClass(propertyAttribute: BasicExcludableProperty::class)]
class ClassWithExcludedProperties
{
    public function __construct(
        #[BasicExcludableProperty(exclude: true)]
        public string $a = '',
        public int $b = 0,
    ) {}
}
