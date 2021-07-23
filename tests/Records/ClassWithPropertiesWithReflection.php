<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassWithReflectableProperties;
use Crell\AttributeUtils\Attributes\PropertyWithReflection;

#[ClassWithReflectableProperties]
class ClassWithPropertiesWithReflection
{
    public int $i;
    #[PropertyWithReflection(name: 'beep')]
    public string $s;
    #[PropertyWithReflection(b: 'B')]
    public float $f;
}
