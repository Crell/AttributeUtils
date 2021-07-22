<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\ClassWithProperties;
use Crell\ObjectAnalyzer\Attributes\BasicProperty;

#[ClassWithProperties]
class ClassWithCustomizedFields
{
    public int $i;
    #[BasicProperty(a: 'A')]
    public string $s;
    #[BasicProperty(b: 'B')]
    public float $f;
}
