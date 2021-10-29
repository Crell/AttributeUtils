<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\GenericClass;
use Crell\AttributeUtils\Attributes\PropertyWithReflection;

#[GenericClass(propertyAttribute: PropertyWithReflection::class)]
class ClassWithPropertiesWithReflection
{
    public int $i;
    #[PropertyWithReflection(name: 'beep')]
    public string $s;
    #[PropertyWithReflection(b: 'B')]
    public float $f;
}
