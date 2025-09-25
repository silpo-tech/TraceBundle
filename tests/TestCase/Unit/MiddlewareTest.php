<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit;

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
        $handler = function ($request, $options) {
            return 'response';
        };

        $wrappedHandler = $middleware($handler);
        $this->assertIsCallable($wrappedHandler);
    }
}
