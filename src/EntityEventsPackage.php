<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\EntityManager\EntityManagerPackage;
use Medas\Events\EventsPackage;
use Medas\ServiceManager\{AsSingleton, BasePackage};

class EntityEventsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            EntityManagerPackage::class,
            EventsPackage::class,
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
