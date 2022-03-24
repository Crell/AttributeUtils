<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface SupportsScopes
{
    /**
     * Whether or not this attribute should be included for a given scope.
     *
     * Note that $scope may be null, which indicates the attribute lookup
     * is happening without a specified scope.  It is up to the attribute
     * to decide if it should be included in that case.
     *
     * Examples:
     * - To include this attribute in a no-scope case, return true for null.
     * - If an attribute has no scope set and you want it to be included in
     *   all scopes, return true unconditionally.
     * - If an attribute has no scope set and you want it to be included
     *   only in a no-scope case, return is_null($scope);
     *
     */
    public function scopes(): array;

//    public function includeUnscopedInScope(): bool;
}
