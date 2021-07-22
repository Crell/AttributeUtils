<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;

/**
 * The most basic class-level attribute. No fancy integration at all.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class BasicClass
{
    public function __construct(
        public int $a = 0,
        public int $b = 0,
    ) {}
}
