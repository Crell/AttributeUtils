<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\GroupedClass;
use Crell\AttributeUtils\Attributes\GroupedClassSub;
use Crell\AttributeUtils\Attributes\GroupedClassSubMulti;
use Crell\AttributeUtils\Attributes\GroupedMethod;
use Crell\AttributeUtils\Attributes\GroupedParam;
use Crell\AttributeUtils\Attributes\GroupedProperty;

#[GroupedClass]
#[GroupedClass(val: 'A', group: 'One')]
#[GroupedClass(val: 'B', group: 'Two')]
#[GroupedClassSub]
#[GroupedClassSub(val: 'A', group: 'One')]
#[GroupedClassSub(val: 'B', group: 'Two')]
#[GroupedClassSubMulti(val: 'X')]
#[GroupedClassSubMulti(val: 'Y')]
#[GroupedClassSubMulti(val: 'A1', group: 'One')]
#[GroupedClassSubMulti(val: 'A2', group: 'One')]
#[GroupedClassSubMulti(val: 'B1', group: 'Two')]
#[GroupedClassSubMulti(val: 'B2', group: 'Two')]
class ClassWithGroups
{
    #[GroupedProperty]
    #[GroupedProperty(val: 'A', group: 'One')]
    #[GroupedProperty(val: 'B', group: 'Two')]
    public string $prop;

    #[GroupedMethod]
    #[GroupedMethod(val: 'A', group: 'One')]
    #[GroupedMethod(val: 'B', group: 'Two')]
    public function aMethod(
        #[GroupedParam]
        #[GroupedParam(val: 'A', group: 'One')]
        #[GroupedParam(val: 'B', group: 'Two')]
        string $param
    ): void {}
}
