<?php

namespace App\Domain\Shared\Events;

use App\Domain\Shared\Contracts\DomainEvent;
use DateTimeImmutable;

abstract class AbstractDomainEvent implements DomainEvent
{
    public function __construct(
        protected readonly DateTimeImmutable $occurredAt = new DateTimeImmutable()
    ) {}

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}

