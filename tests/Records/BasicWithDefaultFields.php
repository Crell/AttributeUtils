<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\BasicClassFielded;

#[BasicClassFielded]
class BasicWithDefaultFields
{
    public int $i;
    public string $s;
    public float $f;
}
