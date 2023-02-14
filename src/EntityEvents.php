<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\EntityEvents\Attributes\{DispatchOnCreation, DispatchOnDeletion, DispatchOnModification};

class EntityEvents
{
    private array $data = [
        DispatchOnCreation::class => [],
        DispatchOnDeletion::class => [],
        DispatchOnModification::class => [],
    ];

    public function add(string $entityName, string $attribute, string $eventClass): void
    {
        $this->data[$attribute][$entityName] = $eventClass;
    }

    public function get(string $entityName, string $attribute): string|null
    {
        return $this->data[$attribute][$entityName] ?? null;
    }

    public function attributes(): array
    {
        return array_keys($this->data);
    }
}
