<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\BasicClassReflectable;

/**
 * A very basic class w no properties.
 */
#[BasicClassReflectable(a: 1)]
class NoProps
{
}
