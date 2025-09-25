<?php

declare(strict_types=1);

namespace TraceBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TraceBundle\Storage\TraceIdStorageInterface;

final readonly class WriteRequestIdToResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TraceIdStorageInterface $traceIdStorage,
        private string $traceIdHeaderName,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [
                ['onResponse', 0],
            ],
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $traceId = $this->traceIdStorage->get();

        if (null !== $traceId) {
            $response->headers->set($this->traceIdHeaderName, $traceId, false);
        }
    }
}
