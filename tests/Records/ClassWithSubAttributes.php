<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\ClassWithPropertiesWithSubAttributes;
use Crell\AttributeUtils\Attributes\ConfigurablePropertyWithSubAttributes;
use Crell\AttributeUtils\Attributes\MultiSubAttribute;
use Crell\AttributeUtils\Attributes\PropertySubAttribute;

#[ClassWithPropertiesWithSubAttributes]
class ClassWithSubAttributes
{
    #[ConfigurablePropertyWithSubAttributes]
    #[PropertySubAttribute]
    #[MultiSubAttribute]
    public int $hasSub = 1;

    #[ConfigurablePropertyWithSubAttributes]
    // No sub-attribute here, to test default handling.
    public int $noSub = 1;
}
