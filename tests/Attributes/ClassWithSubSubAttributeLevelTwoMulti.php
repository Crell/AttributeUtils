<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Multivalue;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ClassWithSubSubAttributeLevelTwoMulti implements Multivalue
{
    public function __construct(public string $d = '') {}
}
