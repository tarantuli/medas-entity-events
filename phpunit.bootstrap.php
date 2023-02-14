<?php

declare(strict_types=1);

use Medas\EntityEvents\EntityEventsPackage;
use Medas\ServiceManager\ServiceManager;

chdir(__DIR__);

ServiceManager::get()
    ->addPackage(EntityEventsPackage::instance());
