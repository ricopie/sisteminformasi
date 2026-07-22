<?php

declare(strict_types=1);

namespace Copie\Shared\Infrastructure;

use Copie\Shared\Domain\DomainEvent;
use Copie\Shared\Domain\EventDispatcherInterface;
use Illuminate\Events\Dispatcher;

class EventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

    public function dispatch(DomainEvent $domainEvent): void
    {
        $this->dispatcher->dispatch($domainEvent);
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
