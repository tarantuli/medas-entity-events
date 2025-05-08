<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Entities\BeforeFlushHandler, Snapshots\Changes};
use Medas\Events\EventDispatcher;

#[Service]
class BeforeChangeHandler implements BeforeFlushHandler
{
    private EntityEvents $eventTypes;

    public function __construct(
        private readonly EntityEventsManager $entityEventsManager,
        private readonly EventDispatcher     $dispatcher,
    )
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
        $this->entityEventsManager = \service(EntityEventsManager::class);
        $this->dispatcher = \service(EventDispatcher::class);
    }

    public function handle(Changes $changes): bool
    {
        if (!isset($this->eventTypes)) {
            $this->eventTypes = $this->entityEventsManager->get();
        }

        $job = new Job();

        $this->handleAttribute(
            $job,
            $changes->createdEntities(),
            Attributes\DispatchBeforeCreation::class
        );

        $this->handleAttribute(
            $job,
            $changes->updatedEntities(),
            Attributes\DispatchBeforeModification::class
        );

        $this->handleAttribute(
            $job,
            $changes->deletedEntities(),
            Attributes\DispatchBeforeDeletion::class
        );

        return $job->dispatchedEvents;
    }

    private function handleAttribute(Job $job, array $entities, string $attribute): void
    {
        foreach ($entities as $entity) {
            $eventClass = $this->eventTypes->get($entity::class, $attribute);

            if ($eventClass) {
                $this->dispatcher->dispatch(new $eventClass($entity));

                $job->dispatchedEvents = true;
            }
        }
    }
}
