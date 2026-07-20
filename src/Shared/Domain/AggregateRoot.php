<?php

declare(strict_types=1);

namespace Copie\Shared\Domain;

abstract class AggregateRoot extends BaseEntity
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    /**
     * Record a domain event to be dispatched after persistence.
     */
    protected function recordDomainEvent(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    /**
     * @return DomainEvent[]
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
