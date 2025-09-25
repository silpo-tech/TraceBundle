<?php

declare(strict_types=1);

namespace TraceBundle\Middleware;

use Sentry\Tracing\GuzzleTracingMiddleware;

final readonly class SentryGuzzleTracingMiddlewareAdapter
{
    public function __construct(
        private ?\Closure $classExistsChecker = null
    ) {
    }

    public function __invoke(callable $handler): callable
    {
        $checker = $this->classExistsChecker ?? fn (string $class) => class_exists($class);

        if (!$checker(GuzzleTracingMiddleware::class)) {
            throw new \LogicException('Cannot use TraceBundle\Middleware\SentryGuzzleTracingMiddlewareAdapter without sentry/sentry installed. Try running "composer require sentry/sentry".');
        }

        return GuzzleTracingMiddleware::trace()($handler);
    }
}
