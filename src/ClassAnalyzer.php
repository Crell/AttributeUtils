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
     * @param class-string|object $class
     *   Either a fully qualified class name or an object to analyze.
     *   Note that if an object is provided, the results may not be cached.
     * @param class-string $attribute
     *   The fully qualified class name of the class attribute to analyze.
     * @return object
     *   The class attribute requested, including dependent data as appropriate.
     */
    public function analyze(string|object $class, string $attribute): object;
}
