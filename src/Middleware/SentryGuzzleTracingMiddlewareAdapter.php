<?php

declare(strict_types=1);

namespace TraceBundle\Middleware;

use LogicException;
use Sentry\Tracing\GuzzleTracingMiddleware;

final class SentryGuzzleTracingMiddlewareAdapter
{
    public function __invoke(callable $handler): callable
    {
        if (!class_exists(GuzzleTracingMiddleware::class)) {
            throw new LogicException('Cannot use TraceBundle\Middleware\SentryGuzzleTracingMiddlewareAdapter without sentry/sentry installed. Try running "composer require sentry/sentry".');
        }

        return GuzzleTracingMiddleware::trace()($handler);
    }
}
