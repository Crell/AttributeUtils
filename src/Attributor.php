<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

class Attributor implements ClassAnalyzer
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

            if ($classDef instanceof ReadsComponents) {
                foreach ($classDef->components() as $component) {
                    $rComponentList = $component->getComponents($subject);

                }

            }

            if ($classDef instanceof FromReflectionClass) {
                $classDef->fromReflection($subject);
            }

            if ($subject instanceof \ReflectionEnum && $classDef instanceof FromReflectionEnum) {
                $classDef->fromReflection($subject);
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
            string: $message,
            format: "Too few arguments to function %s::%s, %d passed in %s on line %d and exactly %d expected"
        );
        [$className, $methodName] = \explode('::', $classAndMethod ?? '');

        throw RequiredAttributeArgumentsMissing::create($className, $error);
    }
}
