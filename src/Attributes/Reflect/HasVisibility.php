<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\Visibility;

trait HasVisibility
{
    /**
     * The visibility of the property.
     */
    public Visibility $visibility;

    protected function parseVisibility(\Reflector $subject)
    {
        $this->visibility = match (true) {
            $subject->isPrivate() => Visibility::Private,
            $subject->isProtected() => Visibility::Protected,
            $subject->isPublic() => Visibility::Public,
        };
    }
}
