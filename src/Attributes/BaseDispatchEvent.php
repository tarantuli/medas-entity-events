<?php

declare(strict_types=1);

namespace Medas\EntityEvents\Attributes;

class BaseDispatchEvent implements DispatchEvent
{
    public function __construct(
        private readonly string $eventClass,
    )
    {
    }

    public function eventClass(): string
    {
        return $this->eventClass;
    }
}
