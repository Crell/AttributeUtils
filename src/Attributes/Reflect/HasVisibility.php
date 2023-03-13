<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\Visibility;

trait HasVisibility
{
    /**
     * The visibility of the property.
     */
    public readonly Visibility $visibility;

    protected function parseVisibility(\Reflector $subject): void
    {
        // The Reflector interface is insufficient, but these methods are defined
        // on all types we care about. This is a reflection API limitation.
        $this->visibility = match (true) {
            // @phpstan-ignore-next-line
            $subject->isPrivate() => Visibility::Private,
            // @phpstan-ignore-next-line
            $subject->isProtected() => Visibility::Protected,
            // @phpstan-ignore-next-line
            $subject->isPublic() => Visibility::Public,
        };
    }
}
