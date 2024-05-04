<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->cacheClass(FileCacheStorage::class);
    $rectorConfig->cacheDirectory('./var/cache/rector');
    $rectorConfig->containerCacheDirectory('./var');

    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_83,
        SetList::CODE_QUALITY,
        SymfonyLevelSetList::UP_TO_SYMFONY_64,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY
    ]);
};

