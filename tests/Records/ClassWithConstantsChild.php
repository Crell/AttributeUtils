<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

class ClassWithConstantsChild extends ClassWithConstantsParent
{
    public const CHILD_ONLY = 'child';

    public const INHERITED = 'inherited';
}
