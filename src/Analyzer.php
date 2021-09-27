<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\Serde\IntersectionTypesNotSupported;
use Crell\Serde\UnionTypesNotSupported;
use function Crell\fp\amap;
use function Crell\fp\afilter;
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
            $fields = $this->getPropertyDefinitions($subject, $classDef::propertyAttribute(), $classDef->includeByDefault());
            $classDef->setProperties($fields);
        }

        // @todo Add support for parsing methods, maybe constants?

        return $classDef;
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

    protected function getPropertyDefinition(\ReflectionProperty $property, string $propertyAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $propDef = $this->getPropertyInheritedAttribute($property, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);
        if ($propDef instanceof FromReflectionProperty) {
            $propDef->fromReflection($property);
        }
        if ($propDef instanceof HasSubAttributes) {
            foreach ($propDef->subAttributes() as $type => $callback) {
                $propDef->$callback($this->getAttribute($property, $type));
            }
        }

        return $propDef;
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

    protected function getPropertyInheritedAttribute(\ReflectionProperty $rProperty, string $attributeType): ?object
    {
        $properties = function () use ($rProperty, $attributeType): \Generator {
            // Check the property iself, first.
            yield $rProperty;

            // Then check the class's parents, if the attribute type is Inheritable.
            if ($this->classImplements($attributeType, Inheritable::class)) {
                // There is no point in scanning ancestor interfaces, as they cannot
                // contain properties. (At least as of PHP 8.1)
                foreach (class_parents($rProperty->getDeclaringClass()->name) as $class) {
                    yield (new \ReflectionClass($class))->getProperty($rProperty->getName());
                }
            }
        };

        $attribute = pipe($properties(),
            firstValue(fn(\ReflectionProperty $rProp): ?object => $this->getAttribute($rProp, $attributeType))
        );

        if ($attribute) {
            return $attribute;
        }

        // Then check the class pointed at by the property, if it exists and the attribute is transitive.
        if ($this->classImplements($attributeType, TransitiveProperty::class)) {
            if ($class = $this->getPropertyClass($rProperty)) {
                return $this->getClassInheritedAttribute($class, $attributeType);
            }
        }

        return null;

    }

    protected function classImplements(string $class, string $interface): bool
    {
        // class_parents() and class_implements() return a parallel k/v array. The key lookup is faster.
        return isset(class_parents($class)[$interface]) || isset(class_implements($class)[$interface]);

        // PHP 8.1 version, which is nicer.
        // return isset([...class_parents($class), ...class_implements($class)][$interface]);
    }
}
