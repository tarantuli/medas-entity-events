<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\Core\Attributes\Service;
use Medas\EntityEvents\Attributes\{DispatchBeforeCreation, DispatchBeforeDeletion, DispatchBeforeModification};
use Medas\EntityManager\Entities\{BeforeFlushHandler, Changes};
use Medas\Events\EventDispatcher;

#[Service]
class BeforeChangeHandler implements BeforeFlushHandler
{
    private EntityEvents $eventTypes;
    private bool $dispatchedEvents = false;

    public function __construct(
        private readonly EntityEventsManager $entityEventsManager,
        private readonly EventDispatcher     $dispatcher,
    )
    {
    }

    public function handle(Changes $changes): bool
    {
        $this->eventTypes = $this->entityEventsManager->get();

        $this->handleAttribute($changes->createdEntities(), DispatchBeforeCreation::class);
        $this->handleAttribute($changes->updatedEntities(), DispatchBeforeModification::class);
        $this->handleAttribute($changes->deletedEntities(), DispatchBeforeDeletion::class);

        return $this->dispatchedEvents;
    }

    private function handleAttribute(array $entities, string $attribute): void
    {
        foreach ($entities as $entity) {
            $eventClass = $this->eventTypes->get($entity::class, $attribute);

            if ($eventClass) {
                $this->dispatcher->dispatch(new $eventClass($entity));
                $this->dispatchedEvents = true;
            }
        }
    }
}
