<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithSubSubAttributeLevelTwo
{
    public function __construct(public string $c = '') {}
}
