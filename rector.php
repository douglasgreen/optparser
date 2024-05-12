<?php

/**
 * Rector configuration file
 *
 * This file is used to configure the Rector PHP code quality tool.
 *
 * Usage:
 * - To perform upgrades, set the environment variable RECTOR_UPGRADE to true.
 *   This should only be done once when upgrading, then disabled again for normal usage.
 * - The file paths for PHP files to analyze come from a file named 'php_paths' in the top-level directory
 *   of the repository. This file should contain PHP files in the top-level directory as well as directories
 *   that contain PHP files. It is shared with other PHP linting utilities so they can all lint the same file list.
 * - The presence of PHPUnit, Symfony, and Doctrine in the composer.json file is automatically detected,
 *   and the relevant Rector rule sets are enabled or disabled accordingly based on the $hasPhpUnit,
 *   $hasSymfony, and $hasDoctrine variables.
 *
 * For more information on configuring Rector, see https://getrector.com/
 */

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

$hasPhpUnit = false;
$hasSymfony = false;
$hasDoctrine = false;
if (file_exists('composer.json')) {
    $composer = file_get_contents('composer.json');
    if ($composer !== false) {
        $hasPhpUnit = preg_match('/\bphpunit\b/', $composer) === 1;
        $hasSymfony = preg_match('/\bsymfony\b/', $composer) === 1;
        $hasDoctrine = preg_match('/\bdoctrine\b/', $composer) === 1;
    }
}

// To do upgrades, set RECTOR_UPGRADE to true in the environment.
// @see https://getrector.com/blog/5-common-mistakes-in-rector-config-and-how-to-avoid-them
$upgrading = (bool) getenv('RECTOR_UPGRADE');
$baseSets = [];
if ($upgrading) {
    $baseSets[] = LevelSetList::UP_TO_PHP_83;
    if ($hasPhpUnit) {
        $baseSets[] = PHPUnitLevelSetList::UP_TO_PHPUNIT_100;
    }

    if ($hasSymfony) {
        $baseSets[] = SymfonyLevelSetList::UP_TO_SYMFONY_64;
    }
} else {
    if ($hasPhpUnit) {
        $baseSets[] = PHPUnitSetList::PHPUNIT_100;
    }

    if ($hasSymfony) {
        $baseSets[] = SymfonySetList::SYMFONY_64;
    }
}

if ($hasPhpUnit) {
    $baseSets[] = PHPUnitSetList::PHPUNIT_CODE_QUALITY;
}

if ($hasSymfony) {
    $baseSets[] = SymfonySetList::SYMFONY_CODE_QUALITY;
    $baseSets[] = SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION;
}

if ($hasDoctrine) {
    $baseSets[] = DoctrineSetList::DOCTRINE_CODE_QUALITY;
}

$paths = file('php_paths');
if ($paths === false) {
    exit("PHP paths not found\n");
}

$paths = array_map('trim', $paths);

return RectorConfig::configure()
    ->withPaths($paths)
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets()
    ->withSets($baseSets)
    ->withCache(cacheDirectory: 'var', cacheClass: FileCacheStorage::class)
    ->withAttributesSets(
        doctrine: $hasDoctrine,
        fosRest: $hasSymfony,
        gedmo: $hasDoctrine,
        jms: $hasDoctrine,
        mongoDb: $hasDoctrine,
        phpunit: $hasPhpUnit,
        sensiolabs: $hasSymfony,
        symfony: $hasSymfony
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
    )
    ->withSkip([AddSeeTestAnnotationRector::class]);
