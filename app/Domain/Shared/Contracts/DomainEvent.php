<?php

namespace App\Domain\Shared\Contracts;

use DateTimeImmutable;

interface DomainEvent
{
    public function eventName(): string;

    public function occurredAt(): DateTimeImmutable;

    public function payload(): array;
}

