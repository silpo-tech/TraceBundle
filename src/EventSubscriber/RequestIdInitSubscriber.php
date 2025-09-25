<?php

declare(strict_types=1);

namespace TraceBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TraceBundle\Generator\TraceIdGeneratorInterface;
use TraceBundle\Storage\TraceIdStorageInterface;

final readonly class RequestIdInitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TraceIdStorageInterface $traceIdStorage,
        private TraceIdGeneratorInterface $traceIdGenerator,
        private string $traceIdHeaderName,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequest', 1536],
            ],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $traceId = $event->getRequest()->headers->get($this->traceIdHeaderName);
        if (null === $traceId) {
            $traceId = $this->traceIdGenerator->generate();
        }

        $this->traceIdStorage->set($traceId);
    }
}
