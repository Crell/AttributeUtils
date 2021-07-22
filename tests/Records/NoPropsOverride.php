<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;

use Crell\ObjectAnalyzer\Attributes\BasicClassReflectable;

/**
 * A very basic class w no properties, but the reflection-derived information is pre-provided.
 */
#[BasicClassReflectable(a: 1, name: 'Overridden')]
class NoPropsOverride
{
}
