<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributor;

use Crell\AttributeUtils\Components\ReadProperties;
use Crell\AttributeUtils\ReadsComponents;

#[\Attribute(\Attribute::TARGET_CLASS)]
class BasicClass implements ReadsComponents
{
    public array $propsA;

    public array $propsB;

    public function components(): iterable
    {
        return [
            new ReadProperties(BasicPropertyA::class, fn(array $props) => $this->propsA = $props),
            new ReadProperties(BasicPropertyB::class, $this->setB(...)),
        ];
    }

    private function setB(array $props): void
    {
        $this->propsB = $props;
    }
}
