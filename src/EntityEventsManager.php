<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\EntityClasses;

#[Service]
readonly class EntityEventsManager
{
    private const CACHE_KEY = 'Medas\EntityEvents\EntityEventsManager::get';

    public function __construct(
        private EntityClasses $entityClasses,
    )
    {
    }

    public function get(): EntityEvents
    {
        return cache(self::CACHE_KEY, fn() => $this->findAll());
    }

    private function findAll(): EntityEvents
    {
        $entityEvents = new EntityEvents();
        $attributeClasses = $entityEvents->attributes();

        foreach ($this->entityClasses->get() as $entityClass) {
            foreach ($attributeClasses as $attributeClass) {
                /** @var Attributes\DispatchEvent $dispatchEvent */
                $dispatchEvent = attribute($attributeClass, new \ReflectionClass($entityClass));

                if ($dispatchEvent) {
                    $entityEvents->add($entityClass, $attributeClass, $dispatchEvent->eventClass());
                }
            }
        }

        return $entityEvents;
    }
}
