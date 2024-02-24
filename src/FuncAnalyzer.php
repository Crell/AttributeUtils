<?php

namespace Crell\AttributeUtils;

class FuncAnalyzer implements FunctionAnalyzer
{
    public function analyze(string|\Closure $function, string $attribute, array $scopes = []): object
    {
        $parser = new AttributeParser($scopes);
        $defBuilder = new ReflectionDefinitionBuilder($parser);

        try {
            $subject = new \ReflectionFunction($function);

            $funcDef = $parser->getAttribute($subject, $attribute) ?? new $attribute;

            if ($funcDef instanceof FromReflectionFunction) {
                $funcDef->fromReflection($subject);
            }

            $defBuilder->loadSubAttributes($funcDef, $subject);

            if ($funcDef instanceof ParseParameters) {
                $parameters = $defBuilder->getDefinitions(
                    $subject->getParameters(),
                    fn (\ReflectionParameter $p)
                    => $defBuilder->getComponentDefinition($p, $funcDef->parameterAttribute(), $funcDef->includeParametersByDefault(), FromReflectionParameter::class, $funcDef)
                );
                $funcDef->setParameters($parameters);
            }

            if ($funcDef instanceof Finalizable) {
                $funcDef->finalize();
            }

            return $funcDef;
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
