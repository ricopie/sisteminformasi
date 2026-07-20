<?php

declare(strict_types=1);

namespace Copie\Shared\Application;

use Copie\Shared\Domain\AggregateRoot;
use Copie\Shared\Domain\EventDispatcherInterface;

/**
 * Base class for command handlers (write side).
 *
 * Provides event dispatching after aggregate persistence.
 * Query handlers should NOT extend this class.
 */
abstract class CommandHandler
{
    public function __construct(
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * Dispatch all domain events recorded by the aggregate since last save.
     */
    protected function dispatchEvents(AggregateRoot $aggregate): void
    {
        foreach ($aggregate->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
