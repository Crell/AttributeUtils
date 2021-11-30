<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

enum MethodType
{
    case Constructor;
    case Destructor;
    case Normal;
}
