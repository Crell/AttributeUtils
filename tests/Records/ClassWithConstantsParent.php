<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassConstant;

class ClassWithConstantsParent
{
    public const PARENT_ONLY = 'parent';

    #[ClassConstant(a: 5)]
    public const INHERITED = 'inherited';
}
