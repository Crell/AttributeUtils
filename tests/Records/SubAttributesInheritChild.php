<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\InheritableClassSubAttributeMain;

#[InheritableClassSubAttributeMain]
class SubAttributesInheritChild extends SubAttributesInheritParent
{
    public string $a = 'A';
}
