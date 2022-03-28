<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface SupportsScopes
{
    /**
     * The scopes this attribute should be included in.
     *
     * To include this attribute in the "no scope requested" case,
     * include `null` in the returned array.
     *
     * In the typical case of an attribute only having a single
     * scope that is specified by an argument, do this:
     *
     * class Attr implements SupportsScopes
     * {
     *     public function __construct(private ?string $scope) {}
     *
     *     public function scopes(): array
     *     {
     *         return [$this->scope];
     *     }
     * }
     *
     * If the intent is to allow multiple scopes on the same
     * attribute instance, you would do this (assuming you want
     * it included it unscoped requests):
     *
     * class Attr implements SupportsScopes
     * {
     *     public function __construct(private array $scopes = [null]) {}
     *
     *     public function scopes(): array
     *     {
     *         return $this->scopes;
     *     }
     * }
     *
     * Returning an empty array means this attribute will never be
     * included, so that is most likely never what you want.
     *
     * To explicitly exclude an attribute in unscoped or scoped requests, implement
     * `Excludable` and mark it excluded in the appropriate scope.
     *
     * @return array<string|null>
     *   An array of scope names in which this attribute should
     *   be included.  Include a value of `null` to have it
     *   present in the "none requested" scope.
     */
    public function scopes(): array;
}
