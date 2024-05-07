<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

// To do upgrades, set RECTOR_UPGRADE to true in the environment.
// @see https://getrector.com/blog/5-common-mistakes-in-rector-config-and-how-to-avoid-them
$upgrading = (bool) getenv('RECTOR_UPGRADE');
if ($upgrading) {
    $baseSets = [
        LevelSetList::UP_TO_PHP_83,
        SymfonyLevelSetList::UP_TO_SYMFONY_64,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_100,
    ];
} else {
    $baseSets = [SymfonySetList::SYMFONY_64, PHPUnitSetList::PHPUNIT_100];
}

$otherSets = [
    DoctrineSetList::DOCTRINE_CODE_QUALITY,
    SymfonySetList::SYMFONY_CODE_QUALITY,
    PHPUnitSetList::PHPUNIT_CODE_QUALITY,
];

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets()
    ->withSets(array_merge($baseSets, $otherSets))
    ->withCache(cacheDirectory: 'var', cacheClass: FileCacheStorage::class)
    ->withAttributesSets(
        doctrine: true,
        fosRest: true,
        gedmo: true,
        jms: true,
        mongoDb: true,
        phpunit: true,
        sensiolabs: true,
        symfony: true
    )
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        deadCode: true,
        earlyReturn: true,
        instanceOf: true,
        naming: true,
        privatization: true,
        strictBooleans: true,
        typeDeclarations: true
    );
