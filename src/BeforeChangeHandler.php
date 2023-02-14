<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\EntityEvents\Attributes\{DispatchBeforeCreation, DispatchBeforeDeletion, DispatchBeforeModification};
use Medas\EntityManager\Entities\{BeforeFlushHandler, Changes};
use Medas\Events\EventDispatcher;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BeforeChangeHandler implements BeforeFlushHandler
{
    private EntityEvents $eventTypes;

    public function __construct(
        private readonly EntityEventsManager $eventTypeHandler,
        private readonly EventDispatcher     $dispatcher,
    )
    {
    }

    public function handle(Changes $changes): void
    {
        $this->eventTypes = $this->eventTypeHandler->get();

        $this->handleAttribute($changes->creates(), DispatchBeforeCreation::class);
        $this->handleAttribute($changes->updates(), DispatchBeforeModification::class);
        $this->handleAttribute($changes->deletes(), DispatchBeforeDeletion::class);
    }

    private function handleAttribute(array $entities, string $attribute): void
    {
        foreach ($entities as $entity) {
            $eventClass = $this->eventTypes->get($entity::class, $attribute);

            if ($eventClass) {
                $this->dispatcher->dispatch(new $eventClass($entity));
            }
        }
    }
}
