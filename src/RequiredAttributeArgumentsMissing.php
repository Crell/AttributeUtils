<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

class RequiredAttributeArgumentsMissing extends \LogicException
{
    public string $attributeType;

    public static function create(string $attributeType, \Throwable $previous): self
    {
        $format = <<<END
The attribute %s has required arguments. If the attribute is set to be included by default,
then it must not be omitted as you must provide the required arguments.
END;
        $message = sprintf($format, $attributeType);

        $new = new self($message, $previous->getCode(), $previous);

        $new->attributeType = $attributeType;

        return $new;
    }
}
