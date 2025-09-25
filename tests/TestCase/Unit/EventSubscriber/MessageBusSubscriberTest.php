<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\EventSubscriber;

use Enqueue\AmqpLib\AmqpContext;
use Interop\Amqp\Impl\AmqpMessage;
use MessageBusBundle\Events\PreConsumeEvent;
use MessageBusBundle\Events\PrePublishEvent;
use PHPUnit\Framework\TestCase;
use TraceBundle\EventSubscriber\MessageBusSubscriber;
use TraceBundle\Tests\TestCase\Unit\MockGeneratorTrait;
use TraceBundle\Storage\TraceIdStorage;

class MessageBusSubscriberTest extends TestCase
{
    use MockGeneratorTrait;

    public function testOk(): void
    {
        $uuid = '0af90dba-f50a-46b2-9e83-b7d2c4b86268';
        $generator = $this->getIdGeneratorMock($uuid);
        $storage = new TraceIdStorage();
        $this->assertEmpty($storage->get());
        $key = 'X-Request-Id';
        $subscriber = new MessageBusSubscriber($storage, $generator, $key);
        $message = new AmqpMessage(json_encode(['property' => 'value']));
        $event = new PreConsumeEvent(
            $message,
            $this->createMock(AmqpContext::class),
            'test',
        );
        $subscriber->preStart($event);
        $id = $storage->get();
        $this->assertEquals($uuid, $id);
        $event = new PrePublishEvent($message);
        $subscriber->prePublish($event);
        $this->assertEquals($id, $event->getMessage()->getCorrelationId());
    }
}
