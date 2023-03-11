<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\PropertyTakesClassDefault;
use Crell\AttributeUtils\Attributes\PropertyTakesClassDefaultClass;

#[PropertyTakesClassDefaultClass(a: 5)]
class PropertyThatTakesClassDefault
{
    public function __construct(
        #[PropertyTakesClassDefault(b: 3)]
        public string $val = 'beep',
    ) {}
}
