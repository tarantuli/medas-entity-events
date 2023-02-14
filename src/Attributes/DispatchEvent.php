<?php

declare(strict_types=1);

namespace Medas\EntityEvents\Attributes;

interface DispatchEvent
{
    public function eventClass(): string;
}
