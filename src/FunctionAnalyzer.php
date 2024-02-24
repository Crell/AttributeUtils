<?php

namespace Crell\AttributeUtils;

interface FunctionAnalyzer
{
    /**
     * Analyzes a function or closure for the specified attribute.
     *
     * @template T of object
     * @param string|\Closure $function
     *   Either a fully qualified function name or a Closure to analyze.
     * @param class-string<T> $attribute
     *   The fully qualified class name of the class attribute to analyze.
     * @param array<string|null> $scopes
     *   The scopes for which this analysis should run.
     * @return T
     *   The function attribute requested, including dependent data as appropriate.
     */
    public function analyze(string|\Closure $function, string $attribute, array $scopes = []): object;
}
