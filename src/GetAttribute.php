<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\firstValue;
use function Crell\fp\pipe;

trait GetAttribute
{
    /**
     * Returns a single attribute of a given type from a target, or null if not found.
     */
    protected function getAttribute(\ReflectionObject|\ReflectionClass|\ReflectionProperty|\ReflectionMethod $target, string $name): ?object
    {
        return $this->getAttributes($target, $name)[0] ?? null;
    }

    /**
     * Get all attributes of a given type from a target.
     *
     * Unfortunately PHP has no common interface for "reflection objects that support attributes",
     * so we have to enumerate them manually.
     */
    protected function getAttributes(\ReflectionObject|\ReflectionClass|\ReflectionProperty|\ReflectionMethod $target, string $name): array
    {
        return array_map(static fn (\ReflectionAttribute $attrib)
        => $attrib->newInstance(), $target->getAttributes($name, \ReflectionAttribute::IS_INSTANCEOF));
    }

    /**
     * Retrieves a single attribute from a class element, including opt-in inheritance and transitiveness.
     *
     * @see getInheritedAttributes()
     */
    protected function getInheritedAttribute(\ReflectionObject|\ReflectionClass|\ReflectionProperty|\ReflectionMethod $target, string $name): ?object{
        return $this->getInheritedAttributes($target, $name)[0] ?? null;
    }

    /**
     * Returns a single attribute of a given type from a target or its ancestors.
     *
     * @todo Can this be folded into getInheritedAttribute, too?
     *
     * @param string $target
     *   The class name for which we want an attribute.
     * @param string $name
     *   The attribute type to retrieve.
     * @return object|null
     *   The attribute object if found on any ancestor, or null if not.
     */
    protected function getClassInheritedAttribute(string $target, string $name): ?object
    {
        $classesToScan = [$target];
        if ($this->classImplements($name, Inheritable::class)) {
            // @todo Remove the array_values() in PHP 8.1, or make it a single wrapping call.
            $subjectAncestors = [...array_values(class_parents($target)), ...array_values(class_implements($target))];
            $classesToScan = [...$classesToScan, ...$subjectAncestors];
        }

        return pipe($classesToScan,
            firstValue(fn (string $c): ?object => $this->getAttribute(new \ReflectionClass($c), $name)),
        );
    }

    /**
     * Retrieves multiple attributes from a class element, including opt-in inheritance and transitiveness.
     *
     * If the attribute in question implements Inheritable, then parent classes
     * will also be checked for the attribute.  If the element is a property that is typed
     * for a class and implements TransitiveProperty, then the class pointed at by the property
     * will also be checked. If it implements both interfaces, then parents of the class
     * pointed to by the property will be checked as well.
     *
     * @param \ReflectionObject|\ReflectionClass|\ReflectionProperty|\ReflectionMethod $target
     *   The property from which to get an attribute.
     * @param string $name
     * @return array
     */
    protected function getInheritedAttributes(\ReflectionObject|\ReflectionClass|\ReflectionProperty|\ReflectionMethod $target, string $name): array
    {
        $attributes = pipe($this->attributeInheritanceTree($target, $name),
            firstValue(fn ($r): array => $this->getAttributes($r, $name))
        );

        if ($attributes) {
            return $attributes;
        }

        // Transitivity is only supported on properties at this time.
        // It's not clear that it makes any sense on methods or constants.
        if ($target instanceof \ReflectionProperty && $this->classImplements($name, TransitiveProperty::class)) {
            if ($class = $this->getPropertyClass($target)) {
                return [$this->getClassInheritedAttribute($class, $name)] ?? [];
            }
        }

        return [];
    }

    /**
     * A generator to produce reflections of all the ancestors of a reflectable.
     *
     * The property itself will be included first, and parents will only be
     * scanned if the attribute implements the Inheritable interface.
     *
     * @see Inheritable
     */
    protected function attributeInheritanceTree(\ReflectionProperty|\ReflectionMethod $subject, string $attributeType): iterable
    {
        // Check the subject itself, first.
        yield $subject;

        [$hasMethod, $getMethod] = match(get_class($subject)) {
            \ReflectionProperty::class => ['hasProperty', 'getProperty'],
            \ReflectionMethod::class => ['hasMethod', 'getMethod'],
        };

        // Then check the class's parents, if the attribute type is Inheritable.
        if ($this->classImplements($attributeType, Inheritable::class)) {
            foreach ($this->classAncestors($subject->getDeclaringClass()->name) as $class) {
                $rClass = new \ReflectionClass($class);
                $subjectName = $subject->getName();
                if ($rClass->$hasMethod($subjectName)) {
                    yield $rClass->$getMethod($subjectName);
                }
            }
        }
    }

    /**
     * Returns a list of all class and interface parents of a class.
     *
     * The class itself is not included in the list.
     */
    protected function classAncestors(string $class): array
    {
        // These methods both return associative arrays, making + safe.
        return class_parents($class) + class_implements($class);
    }
}
