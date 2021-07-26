<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

trait GetAttribute
{
    /**
     * Returns a single attribute of a given type from a target, or null if not found.
     */
    protected function getAttribute(\ReflectionObject|\ReflectionClass|\ReflectionProperty $target, string $name): ?object
    {
        return $this->getAttributes($target, $name)[0] ?? null;
    }

    /**
     * Get all attributes of a given type from a target.
     *
     * Unfortunately PHP has no common interface for "reflection objects that support attributes",
     * so we have to enumerate them manually.
     */
    protected function getAttributes(\ReflectionObject|\ReflectionClass|\ReflectionProperty $target, string $name): array
    {
        return array_map(static fn (\ReflectionAttribute $attrib)
        => $attrib->newInstance(), $target->getAttributes($name, \ReflectionAttribute::IS_INSTANCEOF));
    }
}
