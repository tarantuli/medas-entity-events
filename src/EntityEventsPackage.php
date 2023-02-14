<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\ServiceManager\{AsSingleton, BasePackage};

class EntityEventsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
        ]);
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
