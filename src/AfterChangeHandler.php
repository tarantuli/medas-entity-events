<?php

declare(strict_types=1);

namespace Medas\EntityEvents;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\{Entities\AfterFlushHandler, Snapshots\Changes};

#[Service]
class AfterChangeHandler extends AbstractChangeHandler implements AfterFlushHandler
{
    public function handle(Changes $changes): bool
    {
        return $this->handleChanges(
            $changes,
            Attributes\DispatchAfterCreation::class,
            Attributes\DispatchAfterModification::class,
            Attributes\DispatchAfterDeletion::class,
        );
    }
}
