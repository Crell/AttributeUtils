<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * List of visibilities allowed by PHP.
 *
 * These are common to properties, methods, constants, and parameters.
 */
enum Visibility
{
    case Public;
    case Protected;
    case Private;
}
