<?php

declare(strict_types=1);

namespace TraceBundle\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TraceBundle\Generator\TraceIdGeneratorInterface;
use TraceBundle\Storage\TraceIdStorageInterface;

final readonly class ConsoleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TraceIdStorageInterface $traceIdStorage,
        private TraceIdGeneratorInterface $traceIdGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => [
                ['__invoke', 512],
            ],
        ];
    }

    public function __invoke(ConsoleCommandEvent $event): void
    {
        if ($this->traceIdStorage->get() === null) {
            $this->traceIdStorage->set($this->traceIdGenerator->generate());
        }
    }
}
