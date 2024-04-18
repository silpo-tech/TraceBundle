<?php

declare(strict_types=1);

namespace TraceBundle\Generator;

interface TraceIdGeneratorInterface
{
    public function generate(): string;
}
