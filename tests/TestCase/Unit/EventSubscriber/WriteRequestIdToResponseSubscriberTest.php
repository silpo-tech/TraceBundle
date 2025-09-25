<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\EventSubscriber;

use Monolog\Test\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use TraceBundle\EventSubscriber\WriteRequestIdToResponseSubscriber;
use TraceBundle\Generator\UuidGenerator;
use TraceBundle\Storage\TraceIdStorage;

class WriteRequestIdToResponseSubscriberTest extends TestCase
{
    public function testOk(): void
    {
        $request = new Request();
        $response = new Response();
        $key = 'X-Request-Id';
        $this->assertEmpty($response->headers->get($key));
        $event = new ResponseEvent(
            $this->createMock(Kernel::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response,
        );
        $storage = new TraceIdStorage();
        $uuid = (new UuidGenerator())->generate();
        $storage->set($uuid);
        $subscriber = new WriteRequestIdToResponseSubscriber($storage, $key);
        $subscriber->onResponse($event);
        $this->assertNotEmpty($response->headers->get($key));
        $this->assertEquals($uuid, $response->headers->get($key));
    }

    public function testGetSubscribedEvents(): void
    {
        $events = WriteRequestIdToResponseSubscriber::getSubscribedEvents();
        
        $this->assertArrayHasKey('kernel.response', $events);
        $this->assertEquals([['onResponse', 0]], $events['kernel.response']);
    }
}
