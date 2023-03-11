<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks an attribute as wanting to be notified when all opt-in methods have been called.
 *
 * In a typical class, setup is done once in the constructor.  Due to the way
 * attributes work, the Class Analyzer builds an attribute in stages, including
 * the constructor (done by PHP itself) and any other opt-in methods.  That makes
 * default logic somewhat more complicated if it could be based on any number of
 * called setup methods, and none of them can guarantee that they are "last".
 *
 * This method will be the last one called, regardless of what else is opted-in to,
 * which allows the attribute to finish any setup and construction logic it needs.
 */
interface Finalizable
{
    public function finalize(): void;
}
