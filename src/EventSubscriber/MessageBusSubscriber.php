<?php

declare(strict_types=1);

namespace TraceBundle\EventSubscriber;

use MessageBusBundle\Events;
use MessageBusBundle\Events\PreConsumeEvent;
use MessageBusBundle\Events\PrePublishEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TraceBundle\Generator\TraceIdGeneratorInterface;
use TraceBundle\Storage\TraceIdStorageInterface;

final class MessageBusSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TraceIdStorageInterface $traceIdStorage,
        private readonly TraceIdGeneratorInterface $traceIdGenerator,
        private readonly string $traceIdHeaderName,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PRODUCER__PRE_PUBLISH => 'prePublish',
            Events::CONSUME__PRE_START => 'preStart',
        ];
    }

    public function prePublish(PrePublishEvent $event): void
    {
        $traceId = $this->traceIdStorage->get();

        if ($traceId !== null) {
            $event->getMessage()->setCorrelationId($traceId);
            $event->getMessage()->setHeader($this->traceIdHeaderName, $traceId);
        }
    }

    public function preStart(PreConsumeEvent $event): void
    {
        $correlationId = $event->getMessage()->getCorrelationId();

        if ($correlationId === null) {
            $correlationId = $this->traceIdGenerator->generate();
        }

        $this->traceIdStorage->set($correlationId);
    }
}
