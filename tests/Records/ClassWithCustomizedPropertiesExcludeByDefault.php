<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\ClassWithProperties;
use Crell\ObjectAnalyzer\Attributes\BasicProperty;

#[ClassWithProperties(include: false)]
class ClassWithCustomizedPropertiesExcludeByDefault
{
    public int $i;
    #[BasicProperty(a: 'A')]
    public string $s;
    #[BasicProperty(b: 'B')]
    public float $f;
}
