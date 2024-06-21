<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClosureSubAttributeInline;
use Crell\AttributeUtils\Attributes\ClosureSubAttributeMain;
use Crell\AttributeUtils\Attributes\ClosureSubAttributeReferenced;

#[ClosureSubAttributeMain]
#[ClosureSubAttributeReferenced('A')]
#[ClosureSubAttributeInline('B')]
class ClassWithClosureSubAttributes
{

}
