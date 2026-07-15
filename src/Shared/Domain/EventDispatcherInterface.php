<?php

declare(strict_types=1);

namespace Copie\Shared\Domain;

interface EventDispatcherInterface
{
    public function dispatch(DomainEvent $domainEvent): void;

    public function dispatchAll(array $events): void;
}
