<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\EntityManager\Snapshots\Changes;

abstract class AbstractChangeHandler
{
    public function __construct(
        private readonly EntityEventsManager $entityEventsManager,
    )
    {
    }

    protected function handleChanges(
        Changes $changes,
        string  $createdAttribute,
        string  $updatedAttribute,
        string  $deletedAttribute,
    ): bool
    {
        $eventTypes = $this->entityEventsManager->get();
        $dispatched = false;

        $map = [
            $createdAttribute => $changes->createdEntities(),
            $updatedAttribute => $changes->updatedEntities(),
            $deletedAttribute => $changes->deletedEntities(),
        ];

        foreach ($map as $attribute => $entities) {
            foreach ($entities as $entity) {
                $eventClass = $eventTypes->get($entity::class, $attribute);

                if ($eventClass) {
                    dispatch(new $eventClass($entity));

                    $dispatched = true;
                }
            }
        }

        return $dispatched;
    }
}
