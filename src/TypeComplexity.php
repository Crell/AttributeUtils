<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

enum TypeComplexity
{
    case Simple;
    case Union;
    case Intersection;
    // For when DNF gets added, eventually, we hope.
    case Compound;
}
