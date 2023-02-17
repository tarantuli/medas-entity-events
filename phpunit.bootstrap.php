<?php

declare(strict_types=1);

use Medas\EntityEvents\EntityEventsPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        EntityEventsPackage::instance(),
    ]);

    return $config;
});
