<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ScopedClassMulti;
use Crell\AttributeUtils\Attributes\ScopedPropertyMulti;

#[ScopedClassMulti]
#[ScopedClassMulti(val: 'A', scopes: ['One'])]
#[ScopedClassMulti(val: 'B', scopes: ['Two'])]
#[ScopedClassMulti(val: 'C', scopes: ['One', 'Two'])]
class ClassWithScopesMulti
{
    #[ScopedPropertyMulti(val: 'A', scopes: ['One'])]
    public string $inOne;

    #[ScopedPropertyMulti]
    #[ScopedPropertyMulti(val: 'A', scopes: ['One'])]
    public string $inOneDefault;

    #[ScopedPropertyMulti(val: 'B', scopes: ['Two'])]
    public string $inTwo;

    #[ScopedPropertyMulti]
    #[ScopedPropertyMulti(val: 'B', scopes: ['Two'])]
    public string $inTwoDefault;

    #[ScopedPropertyMulti(val: 'A', scopes: ['One', 'Two'])]
    public string $inOneTwo;

    #[ScopedPropertyMulti]
    #[ScopedPropertyMulti(val: 'A', scopes: ['One', 'Two'])]
    public string $inOneTwoDefault;

    #[ScopedPropertyMulti(val: 'C', scopes: ['Three'])]
    public string $inThree;
}
