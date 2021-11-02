<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use PhpBench\Reflection\ReflectionClass;
use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\firstValue;
use function Crell\fp\indexBy;
use function Crell\fp\pipe;

class Analyzer implements ClassAnalyzer
{
    use GetAttribute;

    public function analyze(string|object $class, string $attribute): object
    {
        // Everything is easier if we normalize to a class first.
        // Because anon classes have generated internal class names, they work, too.
        $class = is_string($class) ? $class : $class::class;

        $subject = new \ReflectionClass($class);

        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $classDef = $this->getClassInheritedAttribute($class, $attribute) ?? new $attribute;

        if ($classDef instanceof FromReflectionClass) {
            $classDef->fromReflection($subject);
        }

        if ($classDef instanceof HasSubAttributes) {
            foreach ($classDef->subAttributes() as $subAttributeType => $callback) {
                $classDef->$callback($this->getClassInheritedAttribute($class, $subAttributeType));
            }
        }

        if ($classDef instanceof ParseProperties) {
            $fields = $this->getPropertyDefinitions($subject, $classDef->propertyAttribute(), $classDef->includePropertiesByDefault());
            $classDef->setProperties($fields);
        }

        if ($classDef instanceof ParseMethods) {
            $methods = $this->getMethodDefinitions($subject, $classDef->methodAttribute(), $classDef->includeMethodsByDefault());
            $classDef->setMethods($methods);
        }

        // @todo Add support for parsing methods, maybe constants?

        return $classDef;
    }

    protected function getMethodDefinitions(\ReflectionClass $subject, string $methodAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getMethods(),
            indexBy(static fn (\ReflectionMethod $r): string => $r->getName()),
            amap(fn (\ReflectionMethod $r) => $this->getMethodDefinition($r, $methodAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $prop):bool => !($prop->exclude ?? false)),
        );
    }

    protected function getMethodDefinition(\ReflectionMethod $rMethod, string $methodAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $methodDef = $this->getInheritedAttribute($rMethod, $methodAttribute)
            ?? ($includeByDefault ?  new $methodAttribute() : null);

        if ($methodDef instanceof FromReflectionMethod) {
            $methodDef->fromReflection($rMethod);
        }
        if ($methodDef instanceof HasSubAttributes) {
            foreach ($methodDef->subAttributes() as $type => $callback) {
                if ($this->isMultivalueAttribute($type)) {
                    $methodDef->$callback($this->getInheritedAttributes($rMethod, $type));
                } else {
                    $methodDef->$callback($this->getInheritedAttribute($rMethod, $type));
                }
            }
        }

        return $methodDef;
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

    protected function getPropertyDefinitions(\ReflectionClass $subject, string $propertyAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getProperties(),
            indexBy(static fn (\ReflectionProperty $r): string => $r->getName()),
            amap(fn (\ReflectionProperty $p) => $this->getPropertyDefinition($p, $propertyAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $prop):bool => !($prop->exclude ?? false)),
        );
    }

    protected function getPropertyDefinition(\ReflectionProperty $rProperty, string $propertyAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $propDef = $this->getInheritedAttribute($rProperty, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);

        if ($propDef instanceof FromReflectionProperty) {
            $propDef->fromReflection($rProperty);
        }
        if ($propDef instanceof HasSubAttributes) {
            foreach ($propDef->subAttributes() as $type => $callback) {
                if ($this->isMultivalueAttribute($type)) {
                    $propDef->$callback($this->getInheritedAttributes($rProperty, $type));
                } else {
                    $propDef->$callback($this->getInheritedAttribute($rProperty, $type));
                }
            }
        }

        return $propDef;
    }

    /**
     * Determines if a given attribute class allows repeating.
     *
     * If passed a non-attribute class, it will return false.
     */
    protected function isMultivalueAttribute(string $attributeType): bool
    {
        $rAttribs = (new \ReflectionClass($attributeType))
            ->getAttributes(\Attribute::class);
        if (!isset($rAttribs[0])) {
            return false;
        }

        return (bool)($rAttribs[0]?->newInstance()?->flags & \Attribute::IS_REPEATABLE);
    }

    /**
     * Returns a single attribute of a given type from a target or its ancestors.
     *
     * @param string $subject
     *   The class name for which we want an attribute.
     * @param string $attributeType
     *   The attribute type to retrieve.
     * @return object|null
     *   The attribute object if found on any ancestor, or null if not.
     */
    protected function getClassInheritedAttribute(string $subject, string $attributeType): ?object
    {
        $classesToScan = [$subject];
        if ($this->classImplements($attributeType, Inheritable::class)) {
            // @todo Remove the array_values() in PHP 8.1, or make it a single wrapping call.
            $subjectAncestors = [...array_values(class_parents($subject)), ...array_values(class_implements($subject))];
            $classesToScan = [...$classesToScan, ...$subjectAncestors];
        }

        return pipe($classesToScan,
            firstValue(fn (string $c): ?object => $this->getAttribute(new \ReflectionClass($c), $attributeType)),
        );
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

    /**
     * Determines if a class name extends or implements a given class/interface.
     *
     * @param string $class
     *   The class name to check.
     * @param string $interface
     *   The class or interface to look for.
     * @return bool
     */
    protected function classImplements(string $class, string $interface): bool
    {
        // class_parents() and class_implements() return a parallel k/v array. The key lookup is faster.
        return $class === $interface || isset(class_parents($class)[$interface]) || isset(class_implements($class)[$interface]);
    }
}
