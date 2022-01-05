<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionEnumCase;

class ReflectEnumCase implements FromReflectionEnumCase
{
    /**
     * The name of the enum, as PHP defines it.
     */
    public string $phpName;

    /**
     * The value of the enum, if it is a backed enum.
     */
    public int|string $value;

    /**
     * True if this is a backed enum, false otherwise.
     */
    public bool $isBacked;

    public function fromReflection(\ReflectionEnumUnitCase $subject): void
    {
        $this->phpName = $subject->getName();

        if ($subject instanceof \ReflectionEnumBackedCase) {
            $this->isBacked = true;
            $this->value = $subject->getBackingValue();
        } else {
            $this->isBacked = false;
        }

        // @todo Do we include doc comment, or the declaring class?
    }
}
