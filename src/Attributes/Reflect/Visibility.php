<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

enum Visibility
{
    case Public;
    case Protected;
    case Private;
}
