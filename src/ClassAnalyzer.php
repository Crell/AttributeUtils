<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Analyzes a class based on a given attribute.
 *
 * The attribute may opt-in to additional processing by implementing
 * one of a series of interfaces.
 */
interface ClassAnalyzer
{
    /**
     * Analyzes a class or object for the specified attribute.
     *
     * @template T of object
     * @param class-string|object $class
     *   Either a fully qualified class name or an object to analyze.
     * @param class-string<T> $attribute
     *   The fully qualified class name of the class attribute to analyze.
     * @param array<string|null> $scopes
     *   The scopes for which this analysis should run.
     * @return T
     *   The class attribute requested, including dependent data as appropriate.
     */
    public function analyze(string|object $class, string $attribute, array $scopes = []): object;
}
