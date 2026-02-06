<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\Core\AsSingleton;
use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\BasePackage;

class EntityEventsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            EntityManagerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
