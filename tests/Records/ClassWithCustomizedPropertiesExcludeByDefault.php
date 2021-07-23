<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Attributes\BasicProperty;

#[ClassWithProperties(include: false)]
class ClassWithCustomizedPropertiesExcludeByDefault
{
    public int $i;
    #[BasicProperty(a: 'A')]
    public string $s;
    #[BasicProperty(b: 'B')]
    public float $f;
}
