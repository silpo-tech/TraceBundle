<?php

declare(strict_types=1);

namespace TraceBundle\Middleware;

use Psr\Http\Message\RequestInterface;
use TraceBundle\Storage\TraceIdStorageInterface;

final readonly class ProxyTraceIdMiddleware
{
    public function __construct(
        private TraceIdStorageInterface $traceIdStorage,
        private string $traceIdHeaderName,
    ) {
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $this->addTraceId($request);

            return $handler($request, $options);
        };
    }

    private function addTraceId(RequestInterface $request): RequestInterface
    {
        $traceId = $this->traceIdStorage->get();
        if ($traceId !== null) {
            $request = $request->withAddedHeader($this->traceIdHeaderName, $traceId);
        }

        return $request;
    }
}
