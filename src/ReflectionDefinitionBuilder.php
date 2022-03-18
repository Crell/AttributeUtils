<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\indexBy;
use function Crell\fp\method;
use function Crell\fp\pipe;

/**
 * Derives the definition for a component's attributes.
 */
class ReflectionDefinitionBuilder
{
    public function __construct(
        protected AttributeParser $parser,
        protected Analyzer $analyzer,
    ) {}

    /**
     * Gets all applicable attribute definitions of a given class element type.
     *
     * Eg, gets all property attributes, or all method attributes.
     *
     * @param \Reflector[] $reflections
     *   The reflection objects to turn into attributes.
     * @param callable $deriver
     *   Callback for turning a reflection object into the corresponding attribute.
     *   It must already have closed over the attribute type to retrieve.
     * @return array
     *   An array of attributes across all items of the applicable type.
     */
    public function getDefinitions(array $reflections, callable $deriver): array
    {
        return pipe($reflections,
            // The Reflector interface is insufficient, but getName() is defined
            // on all types we care about. This is a reflection API limitation.
            // @phpstan-ignore-next-line
            indexBy(method('getName')),
            amap($deriver),
            afilter(static fn (?object $attr): bool => $attr && !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    /**
     * Returns the attribute definition for a class component.
     */
    public function getComponentDefinition(\Reflector $reflection, string $attributeType, bool $includeByDefault, string $reflectionInterface): ?object
    {
        $def = $this->parser->getInheritedAttribute($reflection, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($def instanceof $reflectionInterface) {
            $def->fromReflection($reflection);
        }

        $this->loadSubAttributes($def, $reflection);

        if ($def instanceof CustomAnalysis) {
            $def->customAnalysis($this->analyzer);
        }

        return $def;
    }

    /**
     * Returns the attribute definition for a method.
     *
     * Methods can't just reuse getComponentDefinition() because they
     * also have parameters of their own to parse.
     */
    public function getMethodDefinition(\ReflectionMethod $reflection, string $attributeType, bool $includeByDefault): ?object
    {
        $def = $this->parser->getInheritedAttribute($reflection, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($def instanceof FromReflectionMethod) {
            $def->fromReflection($reflection);
        }

        $this->loadSubAttributes($def, $reflection);

        if ($def instanceof ParseParameters) {
            $parameters = $this->getDefinitions(
                $reflection->getParameters(),
                fn (\ReflectionParameter $p)
                => $this->getComponentDefinition($p, $def->parameterAttribute(), $def->includeParametersByDefault(), FromReflectionParameter::class)
            );
            $def->setParameters($parameters);
        }

        if ($def instanceof CustomAnalysis) {
            $def->customAnalysis($this->analyzer);
        }

        return $def;
    }

    /**
     * Loads sub-attributes onto an attribute, if appropriate.
     */
    public function loadSubAttributes(?object $attribute, \Reflector $reflection): void
    {
        if ($attribute instanceof HasSubAttributes) {
            foreach ($attribute->subAttributes() as $type => $callback) {
                if ($this->isMultivalueAttribute($type)) {
                    $subs = $this->parser->getInheritedAttributes($reflection, $type);
                    foreach ($subs as $sub) {
                        $this->loadSubAttributes($sub, $reflection);
                    }
                    $attribute->$callback($subs);
                } else {
                    $sub = $this->parser->getInheritedAttribute($reflection, $type);
                    $this->loadSubAttributes($sub, $reflection);
                    $attribute->$callback($sub);
                }
            }
        }
    }

    /**
     * Determines if a given attribute class allows repeating.
     *
     * This is only meaningful for attributes used as sub-attributes.
     */
    protected function isMultivalueAttribute(string $attributeType): bool
    {
        return is_a($attributeType, Multivalue::class, true);
    }
}
