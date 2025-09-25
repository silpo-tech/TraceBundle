<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\EventSubscriber;

use Monolog\Test\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use TraceBundle\EventSubscriber\RequestIdInitSubscriber;
use TraceBundle\Generator\UuidGenerator;
use TraceBundle\Storage\TraceIdStorage;
use TraceBundle\Tests\Helper\MockGeneratorTrait;
use TraceBundle\Tests\TestKernel;

class RequestIdInitSubscriberTest extends TestCase
{
    use MockGeneratorTrait;

    public function testWithoutHeader(): void
    {
        $uuid = '0af90dba-f50a-46b2-9e83-b7d2c4b86268';
        $generator = $this->getIdGeneratorMock($uuid);
        $request = new Request();
        $storage = new TraceIdStorage();
        $key = 'X-Request-Id';
        $this->assertEmpty($storage->get());
        $subscriber = new RequestIdInitSubscriber($storage, $generator, $key);
        $event = new RequestEvent(
            new TestKernel('test', true),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
        $subscriber->onRequest($event);
        $this->assertEquals($uuid, $storage->get());
    }

    public function testWithHeader(): void
    {
        $request = new Request();
        $storage = new TraceIdStorage();
        $key = 'X-Request-Id';
        $request->headers->set($key, 'test-uuid');
        $this->assertEmpty($storage->get());
        $subscriber = new RequestIdInitSubscriber($storage, new UuidGenerator(), $key);
        $event = new RequestEvent(
            new TestKernel('test', true),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
        $subscriber->onRequest($event);
        $this->assertNotEmpty($storage->get());
        $this->assertEquals('test-uuid', $storage->get());
    }

    public function testGetSubscribedEvents(): void
    {
        $events = RequestIdInitSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey('kernel.request', $events);
        $this->assertEquals([['onRequest', 1536]], $events['kernel.request']);
    }
}
