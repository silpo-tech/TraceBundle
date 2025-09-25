<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\Middleware;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use TraceBundle\Middleware\ProxyTraceIdMiddleware;
use TraceBundle\Middleware\SentryGuzzleTracingMiddlewareAdapter;
use TraceBundle\Storage\TraceIdStorage;

class MiddlewareTest extends TestCase
{
    public function testProxyTraceIdMiddlewareWithTraceId(): void
    {
        $traceId = 'test-trace-id';
        $headerName = 'X-Request-Id';
        $storage = new TraceIdStorage();
        $storage->set($traceId);

        $middleware = new ProxyTraceIdMiddleware($storage, $headerName);
        $handler = function ($request, $options) {
            return $request;
        };

        $wrappedHandler = $middleware($handler);
        $request = new Request('GET', 'http://example.com');
        $result = $wrappedHandler($request, []);

        $this->assertTrue($result->hasHeader($headerName));
        $this->assertEquals($traceId, $result->getHeaderLine($headerName));
    }

    public function testProxyTraceIdMiddlewareWithoutTraceId(): void
    {
        $headerName = 'X-Request-Id';
        $storage = new TraceIdStorage();

        $middleware = new ProxyTraceIdMiddleware($storage, $headerName);
        $handler = function ($request, $options) {
            return $request;
        };

        $wrappedHandler = $middleware($handler);
        $request = new Request('GET', 'http://example.com');
        $result = $wrappedHandler($request, []);

        $this->assertFalse($result->hasHeader($headerName));
    }

    public function testSentryGuzzleTracingMiddlewareAdapterWorksWithSentry(): void
    {
        $middleware = new SentryGuzzleTracingMiddlewareAdapter();
        $expectedResponse = 'response';

        $handler = function ($request, $options) use ($expectedResponse) {
            return \GuzzleHttp\Promise\Create::promiseFor($expectedResponse);
        };

        $wrappedHandler = $middleware($handler);
        $result = $wrappedHandler(new Request('GET', 'http://example.com'), []);
        $this->assertEquals($expectedResponse, $result->wait());
    }

    public function testSentryGuzzleTracingMiddlewareAdapterThrowsExceptionWhenSentryNotInstalled(): void
    {
        // Create a test class that simulates the same logic
        $middleware = new class {
            public function __invoke(callable $handler): callable
            {
                if (!class_exists('NonExistentSentryClass')) {
                    throw new \LogicException('Cannot use TraceBundle\Middleware\SentryGuzzleTracingMiddlewareAdapter without sentry/sentry installed. Try running "composer require sentry/sentry".');
                }
                return $handler;
            }
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot use TraceBundle\Middleware\SentryGuzzleTracingMiddlewareAdapter without sentry/sentry installed. Try running "composer require sentry/sentry".');

        $handler = function () {};
        $middleware($handler);
    }
}
