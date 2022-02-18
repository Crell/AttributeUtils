<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use \Attribute;
use Crell\AttributeUtils\HasSubAttributes;
use function Crell\fp\amap;
use function Crell\fp\prop;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithSubSubAttributeLevelOne implements HasSubAttributes
{
    public ?ClassWithSubSubAttributeLevelTwo $sub;
    public array $d;

    public function __construct(public string $b = '') {}

    public function subAttributes(): array
    {
        return [
            ClassWithSubSubAttributeLevelTwo::class => 'fromSubAttribute',
            ClassWithSubSubAttributeLevelTwoMulti::class => 'fromMultiSubAttributes',
        ];
    }

    public function fromSubAttribute(?ClassWithSubSubAttributeLevelTwo $sub): void
    {
        $this->sub = $sub;
    }

    public function fromMultiSubAttributes(array $subs): void
    {
        $this->d = amap(prop('d'))($subs);
    }
}
