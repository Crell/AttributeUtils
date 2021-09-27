<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;
use Crell\AttributeUtils\Attributes\InheritableClassAttributeMain;
use Crell\AttributeUtils\Attributes\InheritableClassSubAttribute;
use Crell\AttributeUtils\Attributes\InheritablePropertyAttributeMain;

#[InheritableClassAttributeMain(a: 2)]
#[InheritableClassSubAttribute(foo: 'baz')]
class AttributesInheritParent
{
    #[InheritablePropertyAttributeMain(a: 4)]
    public string $test = 'stuff';
}
