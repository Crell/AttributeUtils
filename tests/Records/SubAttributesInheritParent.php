<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;
use Crell\AttributeUtils\Attributes\InheritableClassSubAttribute;
use Crell\AttributeUtils\Attributes\InheritableClassSubAttributeMain;

#[InheritableClassSubAttributeMain]
#[InheritableClassSubAttribute(foo: 'baz')]
class SubAttributesInheritParent
{

}
