<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ScopedClass;
use Crell\AttributeUtils\Attributes\ScopedClassSub;
use Crell\AttributeUtils\Attributes\ScopedClassSubMulti;
use Crell\AttributeUtils\Attributes\ScopedMethod;
use Crell\AttributeUtils\Attributes\ScopedParam;
use Crell\AttributeUtils\Attributes\ScopedProperty;

#[ScopedClass]
#[ScopedClass(val: 'A', scope: 'One')]
#[ScopedClass(val: 'B', scope: 'Two')]
#[ScopedClassSub]
#[ScopedClassSub(val: 'A', scope: 'One')]
#[ScopedClassSub(val: 'B', scope: 'Two')]
#[ScopedClassSubMulti(val: 'X')]
#[ScopedClassSubMulti(val: 'Y')]
#[ScopedClassSubMulti(val: 'A1', scope: 'One')]
#[ScopedClassSubMulti(val: 'A2', scope: 'One')]
#[ScopedClassSubMulti(val: 'B1', scope: 'Two')]
#[ScopedClassSubMulti(val: 'B2', scope: 'Two')]
class ClassWithScopes
{
    public string $noAttrib;

    #[ScopedProperty]
    #[ScopedProperty(val: 'A', scope: 'One')]
    #[ScopedProperty(val: 'B', scope: 'Two')]
    public string $scoped;

    #[ScopedProperty]
    public string $defaultAttrib;

    #[ScopedProperty(exclude: true)]
    public string $defaultAttribExcluded;

    #[ScopedProperty(val: 'A', scope: 'One', includeUnscopedInScope: false)]
    #[ScopedProperty(val: 'B', scope: 'Two', includeUnscopedInScope: false)]
    public string $notNullScope;

    #[ScopedProperty]
    #[ScopedProperty(val: 'A', scope: 'One', exclude: true)]
    #[ScopedProperty(val: 'B', scope: 'Two', exclude: true)]
    public string $excludeOnlyInScopes;

    #[ScopedProperty(includeUnscopedInScope: false)]
    #[ScopedProperty(val: 'A', scope: 'One', includeUnscopedInScope: false)]
    #[ScopedProperty(val: 'B', scope: 'Two', includeUnscopedInScope: false)]
    public string $excludeFromNullScopeHasScope;

    #[ScopedProperty(val: 'A', scope: 'One')]
    public string $onlyInScopeOne;

    #[ScopedMethod]
    #[ScopedMethod(val: 'A', scope: 'One')]
    #[ScopedMethod(val: 'B', scope: 'Two')]
    public function aMethod(
        #[ScopedParam]
        #[ScopedParam(val: 'A', scope: 'One')]
        #[ScopedParam(val: 'B', scope: 'Two')]
        string $param
    ): void {}
}
