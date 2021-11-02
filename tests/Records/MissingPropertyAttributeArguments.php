<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\GenericClass;
use Crell\AttributeUtils\Attributes\HasRequiredArgs;

#[GenericClass(propertyAttribute: HasRequiredArgs::class)]
class MissingPropertyAttributeArguments
{
    // No attribute defined, but the expected attribute has required
    // arguments, so this is invalid.
    public int $missing;
}
