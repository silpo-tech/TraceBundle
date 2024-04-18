<?php

declare(strict_types=1);

namespace TraceBundle\Storage;

use Symfony\Contracts\Service\ResetInterface;

final class TraceIdStorage implements ResetInterface, TraceIdStorageInterface
{
    public string|null $traceId = null;

    public function __construct()
    {
    }

    public function get(): string|null
    {
        return $this->traceId;
    }

    /** TODO mb just once */
    public function set(string $traceId): self
    {
        $this->traceId = $traceId;

        return $this;
    }

    public function reset(): void
    {
        $this->traceId = null;
    }
}
