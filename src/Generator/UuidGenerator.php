<?php

declare(strict_types=1);

namespace TraceBundle\Generator;

use Ramsey\Uuid\Uuid;

final class UuidGenerator implements TraceIdGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
