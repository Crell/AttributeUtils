<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassWithPropertiesWithSubAttributes;
use Crell\AttributeUtils\Attributes\PropertyWithSubAttributes;
use Crell\AttributeUtils\Attributes\PropertySubAttribute;

#[ClassWithPropertiesWithSubAttributes]
class ClassWithSubAttributes
{
    #[PropertyWithSubAttributes]
    #[PropertySubAttribute]
    public int $hasSub = 1;

    #[PropertyWithSubAttributes]
    // No sub-attribute here, to test default handling.
    public int $noSub = 1;
}
