<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\EntityEvents\Attributes\DispatchOnCreation;
use Medas\EntityEvents\Attributes\DispatchOnDeletion;
use Medas\EntityEvents\Attributes\DispatchOnModification;
use Medas\EntityManager\Entities\AfterFlushHandler;
use Medas\EntityManager\Entities\Changes;
use Medas\Events\EventDispatcher;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ChangeHandler implements AfterFlushHandler
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

        $this->handleAttribute($changes->creates(), DispatchOnCreation::class);
        $this->handleAttribute($changes->updates(), DispatchOnModification::class);
        $this->handleAttribute($changes->deletes(), DispatchOnDeletion::class);
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
