<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class BasicProperty
{
    public function __construct(
        public string $a = 'a',
        public string $b = 'b',
    ) {}
}
