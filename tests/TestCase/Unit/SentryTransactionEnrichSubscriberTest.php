<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit;

use Monolog\Test\TestCase;
use Sentry\SentrySdk;
use Sentry\Tracing\TransactionContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use TraceBundle\EventSubscriber\SentryTransactionEnrichSubscriber;
use TraceBundle\Generator\UuidGenerator;
use TraceBundle\Storage\TraceIdStorage;

class SentryTransactionEnrichSubscriberTest extends TestCase
{
    public function testOk(): void
    {
        $request = new Request();
        $response = new Response();
        $key = 'X-Request-Id';
        $this->assertEmpty($response->headers->get($key));
        $event = new RequestEvent(
            $this->createMock(Kernel::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
        $storage = new TraceIdStorage();
        $uuid = (new UuidGenerator())->generate();
        $storage->set($uuid);
        $sentryTransactionContext = new TransactionContext();

        $sentryTransaction = SentrySdk::getCurrentHub()->startTransaction($sentryTransactionContext);
        SentrySdk::getCurrentHub()->setSpan($sentryTransaction);

        $subscriber = new SentryTransactionEnrichSubscriber($storage, $key);
        $subscriber->addRequestIdToSentryTransaction($event);

        $this->assertEquals($uuid, $sentryTransaction->getTags()[$key] ?? null);
    }
}
