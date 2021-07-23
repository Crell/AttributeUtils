<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassWithProperties;

#[ClassWithProperties]
class ClassWithDefaultFields
{
    public int $i;
    public string $s;
    public float $f;
}
