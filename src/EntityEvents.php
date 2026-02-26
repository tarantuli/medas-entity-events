<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

class EntityEvents
{
    private array $data = [
        Attributes\DispatchBeforeCreation::class => [],
        Attributes\DispatchBeforeDeletion::class => [],
        Attributes\DispatchBeforeModification::class => [],
        Attributes\DispatchAfterCreation::class => [],
        Attributes\DispatchAfterDeletion::class => [],
        Attributes\DispatchAfterModification::class => [],
    ];

    public function add(string $entityName, string $attribute, string $eventClass): void
    {
        if (!array_key_exists($attribute, $this->data)) {
            throw new Exceptions\UnknownDispatchAttribute($attribute);
        }

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
