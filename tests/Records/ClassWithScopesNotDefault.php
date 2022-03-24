<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ScopedClass;
use Crell\AttributeUtils\Attributes\ScopedClassNoDefaultInclude;
use Crell\AttributeUtils\Attributes\ScopedClassSub;
use Crell\AttributeUtils\Attributes\ScopedClassSubMulti;
use Crell\AttributeUtils\Attributes\ScopedMethod;
use Crell\AttributeUtils\Attributes\ScopedParam;
use Crell\AttributeUtils\Attributes\ScopedProperty;

#[ScopedClassNoDefaultInclude]
#[ScopedClassNoDefaultInclude(val: 'A', scope: 'One')]
#[ScopedClassNoDefaultInclude(val: 'B', scope: 'Two')]
#[ScopedClassSub(includeInAll: false)]
#[ScopedClassSub(val: 'A', scope: 'One')]
#[ScopedClassSub(val: 'B', scope: 'Two')]
#[ScopedClassSubMulti(val: 'X')]
#[ScopedClassSubMulti(val: 'Y')]
#[ScopedClassSubMulti(val: 'A1', scope: 'One')]
#[ScopedClassSubMulti(val: 'A2', scope: 'One')]
#[ScopedClassSubMulti(val: 'B1', scope: 'Two')]
#[ScopedClassSubMulti(val: 'B2', scope: 'Two')]
class ClassWithScopesNotDefault
{
    #[ScopedProperty]
    #[ScopedProperty(val: 'A', scope: 'One')]
    #[ScopedProperty(val: 'B', scope: 'Two')]
    public string $prop;

    public string $noAttrib;

    #[ScopedMethod]
    #[ScopedMethod(val: 'A', scope: 'One')]
    #[ScopedMethod(val: 'B', scope: 'Two')]
    public function aMethod(
        #[ScopedParam(includeInAll: false)]
        #[ScopedParam(val: 'A', scope: 'One')]
        #[ScopedParam(val: 'B', scope: 'Two')]
        string $param
    ): void {}
}
