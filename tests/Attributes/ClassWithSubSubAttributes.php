<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use \Attribute;
use Crell\AttributeUtils\HasSubAttributes;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithSubSubAttributes implements HasSubAttributes
{
    public ?ClassWithSubSubAttributeLevelOne $sub;

    public function __construct(
        public string $a = '',
    ) {}

    public function subAttributes(): array
    {
        return [ClassWithSubSubAttributeLevelOne::class => 'fromSubAttribute'];
    }

    public function fromSubAttribute(?ClassWithSubSubAttributeLevelOne $sub): void
    {
        $this->sub = $sub;
    }
}
