<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\SubattributeReflection;

#[ClassAllFeaturesForSubAttrib]
#[SubAttributeReflect]
enum EnumForSubAttrib
{
    #[ComponentAttribute]
    #[SubAttributeReflect]
    case Case;
}
