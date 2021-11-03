<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\firstValue;
use function Crell\fp\pipe;

class AttributeParser
{
    /**
     * Returns a single attribute of a given type from a target, or null if not found.
     */
    public function getAttribute(\Reflector $target, string $name): ?object
    {
        return $this->getAttributes($target, $name)[0] ?? null;
    }

    /**
     * Get all attributes of a given type from a target.
     *
     * Unfortunately PHP has no common interface for "reflection objects that support attributes",
     * and enumerating them manually is stupidly verbose and clunky. Instead just refer
     * to any reflectable thing and hope for the best.
     */
    public function getAttributes(\Reflector $target, string $name): array
    {
        return array_map(static fn (\ReflectionAttribute $attrib)
           => $attrib->newInstance(), $target->getAttributes($name, \ReflectionAttribute::IS_INSTANCEOF));
    }

    /**
     * Retrieves a single attribute from a class element, including opt-in inheritance and transitiveness.
     *
     * @see getInheritedAttributes()
     */
    public function getInheritedAttribute(\Reflector $target, string $name): ?object
    {
        return $this->getInheritedAttributes($target, $name)[0] ?? null;
    }

    /**
     * Retrieves multiple attributes from a class element, including opt-in inheritance and transitiveness.
     *
     * If the attribute in question implements Inheritable, then parent classes
     * will also be checked for the attribute.
     *
     * If the element is a property that is typed for a class and implements
     * TransitiveProperty, then the class pointed at by the property will also be
     * checked. If it implements both interfaces, then parents of the class
     * pointed to by the property will be checked as well.
     *
     * @param \Reflector $target
     *   The property from which to get an attribute.
     * @param string $name
     * @return array
     */
    public function getInheritedAttributes(\Reflector $target, string $name): array
    {
        $attributes = pipe($this->attributeInheritanceTree($target, $name),
            firstValue(fn ($r): array => $this->getAttributes($r, $name))
        );

        if ($attributes) {
            return $attributes;
        }

        // Transitivity is only supported on properties at this time.
        // It's not clear that it makes any sense on methods or constants.
        if ($target instanceof \ReflectionProperty
            && $this->classImplements($name,TransitiveProperty::class)
            && $class = $this->getPropertyClass($target))
        {
            return pipe($this->classAncestors($class),
                firstValue(fn (string $c): array => $this->getAttributes(new \ReflectionClass($c), $name)),
            ) ?? [];
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
    protected function attributeInheritanceTree(\Reflector $subject, string $attributeType): iterable
    {
        // Check the subject itself, first.
        yield $subject;

        if ($this->classImplements($attributeType, Inheritable::class)) {
            yield from match(get_class($subject)) {
                \ReflectionClass::class => $this->classInheritanceTree($subject),
                \ReflectionObject::class => $this->classInheritanceTree($subject),
                \ReflectionProperty::class => $this->classElementInheritanceTree($subject),
                \ReflectionMethod::class => $this->classElementInheritanceTree($subject),
                \ReflectionClassConstant::class => $this->classElementInheritanceTree($subject),
                \ReflectionParameter::class => $this->parameterInheritanceTree($subject),
            };
        }
    }

    protected function classInheritanceTree(\ReflectionClass $subject): iterable
    {
        $ancestors = $this->classAncestors($subject->getName(), false);
        foreach ($ancestors as $ancestor) {
            yield new \ReflectionClass($ancestor);
        }
    }

    protected function parameterInheritanceTree(\ReflectionParameter $subject): iterable
    {
        $parameterName = $subject->getName();
        $methodName = $subject->getDeclaringFunction()->name;

        foreach ($this->classAncestors($subject->getDeclaringClass()->name) as $class) {
            $rClass = new \ReflectionClass($class);
            if ($rClass->hasMethod($methodName)) {
                $rMethod = $rClass->getMethod($parameterName);
                foreach ($rMethod->getParameters() as $rParam) {
                    if ($rParam->name === $parameterName) {
                        yield $rParam;
                        break;
                    }
                }
            }
        }
    }

    protected function classElementInheritanceTree(\ReflectionProperty|\ReflectionMethod|\ReflectionClassConstant $subject): iterable
    {
        $subjectName = $subject->getName();

        [$hasMethod, $getMethod] = match(get_class($subject)) {
            \ReflectionProperty::class => ['hasProperty', 'getProperty'],
            \ReflectionMethod::class => ['hasMethod', 'getMethod'],
            \ReflectionClassConstant::class => ['hasConstant', 'getReflectionConstant'],
        };

        foreach ($this->classAncestors($subject->getDeclaringClass()->name) as $class) {
            $rClass = new \ReflectionClass($class);
            if ($rClass->$hasMethod($subjectName)) {
                yield $rClass->$getMethod($subjectName);
            }
        }
    }

    /**
     * Returns a list of all class and interface parents of a class.
     */
    public function classAncestors(string $class, bool $includeClass = true): array
    {
        // These methods both return associative arrays, making + safe.
        $ancestors = class_parents($class) + class_implements($class);
        return $includeClass
            ? [$class => $class] + $ancestors
            : $ancestors
        ;
    }


    /**
     * Determines if a class name extends or implements a given class/interface.
     *
     * @param string $class
     *   The class name to check.
     * @param string $interface
     *   The class or interface to look for.
     * @return bool
     */
    public function classImplements(string $class, string $interface): bool
    {
        // class_parents() and class_implements() return a parallel k/v array. The key lookup is faster.
        return isset($this->classAncestors($class)[$interface]);
    }

    /**
     * Returns the class or interface a given property is typed for, or null if it's not so typed.
     *
     * @param \ReflectionProperty $rProperty
     *   The property to check
     * @return string|null
     *   The class/interface name, or null.
     */
    protected function getPropertyClass(\ReflectionProperty $rProperty): ?string
    {
        $rType = $rProperty->getType();
        if ($rType instanceof \ReflectionNamedType && (class_exists($rType->getName()) || interface_exists($rType->getName()))) {
            return $rType->getName();
        }
        return null;
    }
}
