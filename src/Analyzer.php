<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

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
        // class_parents() and class_implements() return a parallel k/v array. The key lookup is faster.
        $attributeAncestors = [...class_parents($attributeType), ...class_implements($attributeType)];
        if (isset($attributeAncestors[Inheritable::class]) ) {
            $subjectAncestors = array_values([...class_parents($subject), ...class_implements($subject)]);
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

    protected function getPropertyInheritedAttribute(\ReflectionProperty $rProperty, string $attributeType): ?object
    {
        $properties = function () use ($rProperty, $attributeType): \Generator {
            yield $rProperty;

            if (!$this->classImplements($attributeType, Inheritable::class)) {
                return null;
            }

            // There is no point in scanning ancestor interfaces, as they cannot
            // contain properties. (At least as of PHP 8.1)
            foreach (class_parents($rProperty->getDeclaringClass()->name) as $class) {
                yield (new \ReflectionClass($class))->getProperty($rProperty->getName());
            }
        };

        return pipe($properties(),
            firstValue(fn(\ReflectionProperty $rProp): ?object => $this->getAttribute($rProp, $attributeType))
        );
    }

    protected function classImplements(string $class, string $interface): bool
    {
        // class_parents() and class_implements() return a parallel k/v array. The key lookup is faster.
        return isset([...class_parents($class), ...class_implements($class)][$interface]);
    }
}
