<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

class Analyzer implements ClassAnalyzer
{

    public function analyze(string|object $class, string $attribute): object
    {
        $subject = match (is_string($class)) {
            true => new \ReflectionClass($class),
            false => new \ReflectionObject($class),
        };

        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $classDef = $this->getAttribute($subject, $attribute) ?? new $attribute;

        if ($classDef instanceof FromReflectionClass) {
            $classDef->fromReflection($subject);
        }

        if ($classDef instanceof ParseProperties) {
            $fields = $this->getPropertyDefinitions($subject, $classDef::propertyAttribute(), $classDef->includeByDefault());
            $classDef->setProperties($fields);
        }

        // @todo Add support for parsing methods, maybe constants?

        return $classDef;
    }

    protected function getPropertyDefinitions(\ReflectionClass $subject, string $propertyAttribute, bool $includeByDefault): array
    {
        // @todo This needs a pipe.
        $rProperties = $subject->getProperties();
        $props = $this->indexBy($rProperties, fn (\ReflectionProperty $r) => $r->getName());
        $properties = array_map(fn(\ReflectionProperty $p) => $this->getPropertyDefinition($p, $propertyAttribute, $includeByDefault), $props);
        $properties = array_filter($properties);
        $properties = array_filter($properties, static fn (object $prop):bool => !($prop->exclude ?? false));
        return $properties;
    }

    /**
     * @todo Break this out to a utility function.
     */
    protected function indexBy(array $arr, callable $keyMaker): array
    {
        $ret = [];
        foreach ($arr as $v) {
            $ret[$keyMaker($v)] = $v;
        }
        return $ret;
    }

    protected function getPropertyDefinition(\ReflectionProperty $property, string $propertyAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $propDef = $this->getAttribute($property, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);
        if ($propDef instanceof FromReflectionProperty) {
            $propDef->fromReflection($property);
        }

        return $propDef;
    }

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
