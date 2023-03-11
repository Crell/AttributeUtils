<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

class Analyzer implements ClassAnalyzer
{
    public function analyze(string|object $class, string $attribute, array $scopes = []): object
    {
        // Everything is easier if we normalize to a class first.
        // Because anon classes have generated internal class names, they work, too.
        $class = is_string($class) ? $class : $class::class;

        // I don't love this special casing, but it's the only way to handle
        // enums themselves being special cases.
        if (function_exists('enum_exists') && enum_exists($class)) {
            $subject = new \ReflectionEnum($class);
        } else {
            $subject = new \ReflectionClass($class);
        }

        $parser = new AttributeParser($scopes);

        $defBuilder = new ReflectionDefinitionBuilder($parser, $this);

        try {
            $classDef = $parser->getInheritedAttribute($subject, $attribute) ?? new $attribute;

            if ($classDef instanceof FromReflectionClass) {
                $classDef->fromReflection($subject);
            }

            if ($subject instanceof \ReflectionEnum && $classDef instanceof FromReflectionEnum) {
                $classDef->fromReflection($subject);
            }

            $defBuilder->loadSubAttributes($classDef, $subject);

            if ($classDef instanceof ParseProperties) {
                $properties = $defBuilder->getDefinitions(
                    // Reflection can get only static, but not only non-static. Because of course.
                    array_filter($subject->getProperties(), static fn (\ReflectionProperty $r) => !$r->isStatic()),
                    fn (\ReflectionProperty $r)
                        => $defBuilder->getComponentDefinition($r, $classDef->propertyAttribute(), $classDef->includePropertiesByDefault(), FromReflectionProperty::class, $classDef)
                );
                $classDef->setProperties($properties);
            }

            if ($classDef instanceof ParseStaticProperties) {
                $properties = $defBuilder->getDefinitions(
                    $subject->getProperties(\ReflectionProperty::IS_STATIC),
                    fn (\ReflectionProperty $r)
                        => $defBuilder->getComponentDefinition($r, $classDef->staticPropertyAttribute(), $classDef->includeStaticPropertiesByDefault(), FromReflectionProperty::class, $classDef)
                );
                $classDef->setStaticProperties($properties);
            }

            if ($classDef instanceof ParseMethods) {
                $methods = $defBuilder->getDefinitions(
                    // Reflection can get only static, but not only non-static. Because of course.
                    array_filter($subject->getMethods(), static fn (\ReflectionMethod $r) => !$r->isStatic()),
                    fn (\ReflectionMethod $r)
                        => $defBuilder->getMethodDefinition($r, $classDef->methodAttribute(), $classDef->includeMethodsByDefault(), $classDef),
                );
                $classDef->setMethods($methods);
            }

            if ($classDef instanceof ParseStaticMethods) {
                $methods = $defBuilder->getDefinitions(
                    $subject->getMethods(\ReflectionMethod::IS_STATIC),
                    fn (\ReflectionMethod $r)
                        => $defBuilder->getMethodDefinition($r, $classDef->staticMethodAttribute(), $classDef->includeStaticMethodsByDefault(), $classDef),
                );
                $classDef->setStaticMethods($methods);
            }

            // Enum cases have to come before constants, because
            // constants will include enums cases.  It's up to the
            // implementing attribute class to filter out the enums
            // from the constants.  Sadly, there is no better API for it.
            if ($subject instanceof \ReflectionEnum && $classDef instanceof ParseEnumCases) {
                $cases = $defBuilder->getDefinitions(
                    $subject->getCases(),
                    fn (\ReflectionEnumUnitCase $r)
                        => $defBuilder->getComponentDefinition($r, $classDef->caseAttribute(), $classDef->includeCasesByDefault(), FromReflectionEnumCase::class, $classDef),
                );
                $classDef->setCases($cases);
            }

            if ($classDef instanceof ParseClassConstants) {
                $constants = $defBuilder->getDefinitions(
                    $subject->getReflectionConstants(),
                    fn (\ReflectionClassConstant $r)
                        => $defBuilder->getComponentDefinition($r, $classDef->constantAttribute(), $classDef->includeConstantsByDefault(), FromReflectionClassConstant::class, $classDef),
                );
                $classDef->setConstants($constants);
            }

            if ($classDef instanceof CustomAnalysis) {
                $classDef->customAnalysis($this);
            }

            if ($classDef instanceof Finalizable) {
                $classDef->finalize();
            }

            return $classDef;
        } catch (\ArgumentCountError $e) {
            $this->translateArgumentCountError($e);
        }
    }

    /**
     * Throws a domain-specific exception based on an ArgumentCountError.
     *
     * This is absolutely hideous, but this is what happens when your throwable
     * puts all the useful information in the message text rather than as useful
     * properties or methods or something.
     *
     * Conclusion: Write better, more debuggable exceptions than PHP does.
     */
    protected function translateArgumentCountError(\ArgumentCountError $error): never
    {
        $message = $error->getMessage();
        // PHPStan doesn't understand this syntax style of sscanf(), so skip it.
        // @phpstan-ignore-next-line
        [$classAndMethod, $passedCount, $file, $line, $expectedCount] = sscanf(
            // @phpstan-ignore-next-line
            string: $message,
            format: "Too few arguments to function %s::%s, %d passed in %s on line %d and exactly %d expected"
        );
        [$className, $methodName] = \explode('::', $classAndMethod);

        throw RequiredAttributeArgumentsMissing::create($className, $error);
    }
}
