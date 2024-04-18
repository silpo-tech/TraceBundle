<?php

declare(strict_types=1);

namespace TraceBundle\Storage;

interface TraceIdStorageInterface
{
    public function get(): string|null;

    public function set(string $traceId): self;
}
