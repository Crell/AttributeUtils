<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\BasicClassReflectableProperties;
use Crell\ObjectAnalyzer\Attributes\BasicPropertyReflectable;

#[BasicClassReflectableProperties]
class BasicWithReflectableProperties
{
    public int $i;
    #[BasicPropertyReflectable(name: 'beep')]
    public string $s;
    #[BasicPropertyReflectable(b: 'B')]
    public float $f;
}
