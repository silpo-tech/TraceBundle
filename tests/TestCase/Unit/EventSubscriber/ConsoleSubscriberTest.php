<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TraceBundle\EventSubscriber\ConsoleSubscriber;
use TraceBundle\Storage\TraceIdStorage;
use TraceBundle\Tests\TestCase\Unit\MockGeneratorTrait;

class ConsoleSubscriberTest extends TestCase
{
    use MockGeneratorTrait;

    public function testOk(): void
    {
        $uuid = '0af90dba-f50a-46b2-9e83-b7d2c4b86268';
        $generator = $this->getIdGeneratorMock($uuid);
        $storage = new TraceIdStorage();
        $this->assertEmpty($storage->get());
        $subscriber = new ConsoleSubscriber($storage, $generator);
        $event = new ConsoleCommandEvent(
            $this->createMock(Command::class),
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
        );
        $subscriber($event);
        $this->assertEquals($uuid, $storage->get());
    }
}
