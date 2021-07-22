<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer\Records;
use Crell\Rekodi\Field;
use Crell\Rekodi\Table;

class Point
{
    public function __construct(
        public int $x,
        public int $y,
        public int $z,
    ) {}
}
