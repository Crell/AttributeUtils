<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\AppliesEverywhere;

#[AppliesEverywhere(1)]
class MultiuseClass
{
    #[AppliesEverywhere(2)]
    public string $prop;

    #[AppliesEverywhere(3)]
    public const B = 'B';

    #[AppliesEverywhere(4)]
    public function method(#[AppliesEverywhere(5)] $arg)
    {

    }
}
