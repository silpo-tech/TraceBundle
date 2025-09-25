<?php

declare(strict_types=1);

namespace TraceBundle\EventSubscriber;

use Sentry\SentrySdk;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TraceBundle\Storage\TraceIdStorageInterface;

final readonly class SentryTransactionEnrichSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TraceIdStorageInterface $traceIdStorage,
        private string $traceIdHeaderName,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['addRequestIdToSentryTransaction', 1200],
            ],
        ];
    }

    public function addRequestIdToSentryTransaction(RequestEvent $event): void
    {
        if (!class_exists(SentrySdk::class)) {
            return;
        }

        $sentryTransaction = SentrySdk::getCurrentHub()->getTransaction();
        $currentRequestTraceId = $this->traceIdStorage->get();
        if (null === $sentryTransaction || null === $currentRequestTraceId) {
            return;
        }

        $sentryTransaction->setTags([$this->traceIdHeaderName => $currentRequestTraceId]);
    }
}
