<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a component as needing to do its own custom analysis.
 *
 * This method is a last-resort.  If an attribute has this interface,
 * the analyzer itself will be passed to this method after all other
 * steps have been taken.  That allows the attribute to further-analyze
 * other sub-components, such as a property wanting to pull in data from
 * the class that it is typed for.  Such cases are rare, and if you can
 * use a different mechanism instead of this interface, you should.
 *
 * Note that the attribute MUST NOT save the analyzer object itself. That
 * would make the attribute object unserializable, and thus uncacheable.
 */
interface CustomAnalysis
{
    public function customAnalysis(ClassAnalyzer $analyzer): void;
}
