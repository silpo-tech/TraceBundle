<?php

declare(strict_types=1);

namespace TraceBundle\Middleware;

use Sentry\Tracing\GuzzleTracingMiddleware;

class SentryGuzzleTracingMiddlewareAdapter
{
    public function __invoke(callable $handler): callable
    {
        if (!$this->sentryClassExists()) {
            throw new \LogicException('Cannot use TraceBundle\Middleware\SentryGuzzleTracingMiddlewareAdapter without sentry/sentry installed. Try running "composer require sentry/sentry".');
        }

        return GuzzleTracingMiddleware::trace()($handler);
    }

    protected function sentryClassExists(): bool
    {
        return class_exists(GuzzleTracingMiddleware::class);
    }
}
