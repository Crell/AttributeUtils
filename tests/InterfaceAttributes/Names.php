<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\InterfaceAttributes;

use Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use function Crell\fp\method;

#[Attribute(Attribute::TARGET_CLASS)]
class Names implements HasSubAttributes, \IteratorAggregate, \ArrayAccess, \Countable
{
    protected readonly array $names;

    public function subAttributes(): array
    {
        return [Name::class => 'fromNames'];
    }

    public function fromNames(array $names): void
    {
        $this->names = $names;
    }

    public function count(): int
    {
        return count($this->names);
    }

    public function nameList(): array
    {
        return array_map(method('fullName'), $this->names);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->names);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->names);
    }

    public function offsetGet(mixed $offset): Alias
    {
        return $this->names[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \InvalidArgumentException();
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \InvalidArgumentException();
    }
}
