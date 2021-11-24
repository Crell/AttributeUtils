<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface CustomAnalysis
{
    public function customAnalysis(ClassAnalyzer $analyzer): void;
}
