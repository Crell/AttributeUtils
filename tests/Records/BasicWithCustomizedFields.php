<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\BasicClassFielded;
use Crell\ObjectAnalyzer\Attributes\BasicProperty;

#[BasicClassFielded]
class BasicWithCustomizedFields
{
    #[BasicProperty]
    public int $i;
    #[BasicProperty(a: 'A')]
    public string $s;
    #[BasicProperty(b: 'B')]
    public float $f;
}
