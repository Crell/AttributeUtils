<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

enum EnumWithInterface: int implements AnInterface
{
    case A = 1;
    case B = 2;
}
