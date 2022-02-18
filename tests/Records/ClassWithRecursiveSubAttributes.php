<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassWithSubSubAttributeLevelOne;
use Crell\AttributeUtils\Attributes\ClassWithSubSubAttributeLevelTwo;
use Crell\AttributeUtils\Attributes\ClassWithSubSubAttributeLevelTwoMulti;
use Crell\AttributeUtils\Attributes\ClassWithSubSubAttributes;

#[ClassWithSubSubAttributes(a: 'A')]
#[ClassWithSubSubAttributeLevelOne(b: 'B')]
#[ClassWithSubSubAttributeLevelTwo(c: 'C')]
#[ClassWithSubSubAttributeLevelTwoMulti(d: 'D')]
#[ClassWithSubSubAttributeLevelTwoMulti(d: 'E')]
#[ClassWithSubSubAttributeLevelTwoMulti(d: 'F')]
class ClassWithRecursiveSubAttributes
{

}
