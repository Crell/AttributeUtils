<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\ClassWithReflectableProperties;
use Crell\ObjectAnalyzer\Attributes\PropertyWithReflection;

#[ClassWithReflectableProperties]
class ClassWithPropertiesWithReflection
{
    public int $i;
    #[PropertyWithReflection(name: 'beep')]
    public string $s;
    #[PropertyWithReflection(b: 'B')]
    public float $f;
}
