<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Entities\BeforeFlushHandler, Snapshots\Changes};

#[Service]
class BeforeChangeHandler extends AbstractChangeHandler implements BeforeFlushHandler
{
    public function handle(Changes $changes): bool
    {
        return $this->handleChanges(
            $changes,
            Attributes\DispatchBeforeCreation::class,
            Attributes\DispatchBeforeModification::class,
            Attributes\DispatchBeforeDeletion::class,
        );
    }
}
