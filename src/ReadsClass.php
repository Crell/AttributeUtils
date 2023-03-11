<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a component-targeting attribute as wanting the class attribute passed to it.
 *
 * If a component-targeting attribute implements this interface, then after it
 * is instantiated and all other opt-in behaviors have run, the corresponding class
 * attribute will be passed to this method.  The attribute may then extract whatever
 * information it desires and save it to properties however it likes.
 *
 * Note that the attribute SHOULD NOT save the class itself in most cases.  It is
 * better to materialize the necessary information onto the attribute, so that it
 * may be accessed more quickly in the future.  Also, circular dependencies get messy.
 */
interface ReadsClass
{
    public function fromClassAttribute(object $class): void;
}
