<?php

/**
 * Easy Coding Standard (ECS) configuration file
 *
 * This file is used to configure the ECS PHP code style and quality tool.
 *
 * Usage:
 * - To perform risky changes, set the environment variable ECS_RISKY to true.
 *   This should be carefully reviewed to ensure it doesn't break anything.
 * - The file paths for PHP files to analyze come from a file named 'php_paths' in the top-level directory
 *   of the repository. This file should contain PHP files in the top-level directory as well as directories
 *   that contain PHP files. It is shared with other PHP linting utilities so they can all lint the same file list.
 * - The presence of PHPUnit, Symfony, and Doctrine in the composer.json file is automatically detected,
 *   and the relevant ECS rule sets are enabled or disabled accordingly based on the $hasPhpUnit,
 *   $hasSymfony, and $hasDoctrine variables.
 * - Be cautious when configuring the list of annotations to remove using the GeneralPhpdocAnnotationRemoveFixer.
 *   ECS removes both the tag and its contents, whereas in many cases, you may only want to remove or modify
 *   the tag itself without affecting its contents.
 *
 * For more information on configuring ECS, see https://github.com/easy-coding-standard/easy-coding-standard
 */

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;

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

// I removed phpCsFixer and phpCsFixerRisky because they conflict with Rector.
$sets = [
    'doctrineAnnotation' => $hasDoctrine,
    'perCS' => true,
    'perCS10' => true,
    'perCS10Risky' => false,
    'perCS20' => true,
    'perCS20Risky' => false,
    'perCSRisky' => false,
    'perRisky' => false,
    'php54Migration' => true,
    'php56MigrationRisky' => false,
    'php70Migration' => true,
    'php70MigrationRisky' => false,
    'php71Migration' => true,
    'php71MigrationRisky' => false,
    'php73Migration' => true,
    'php74Migration' => true,
    'php74MigrationRisky' => false,
    'php80Migration' => true,
    'php80MigrationRisky' => false,
    'php81Migration' => true,
    'php82Migration' => true,
    'php83Migration' => true,
    'phpunit30MigrationRisky' => false,
    'phpunit32MigrationRisky' => false,
    'phpunit35MigrationRisky' => false,
    'phpunit43MigrationRisky' => false,
    'phpunit48MigrationRisky' => false,
    'phpunit50MigrationRisky' => false,
    'phpunit52MigrationRisky' => false,
    'phpunit54MigrationRisky' => false,
    'phpunit55MigrationRisky' => false,
    'phpunit56MigrationRisky' => false,
    'phpunit57MigrationRisky' => false,
    'phpunit60MigrationRisky' => false,
    'phpunit75MigrationRisky' => false,
    'phpunit84MigrationRisky' => false,
    'phpunit100MigrationRisky' => false,
    'psr1' => true,
    'psr2' => true,
    'psr12' => true,
    'psr12Risky' => false,
    'symfony' => $hasSymfony,
    'symfonyRisky' => false,
];

// To do risky changes, set ECS_RISKY to true in the environment.
$useRisky = (bool) getenv('ECS_RISKY');
if ($useRisky) {
    foreach (array_keys($sets) as $set) {
        if (preg_match('/^phpunit/', $set) === 1) {
            $sets[$set] = $hasPhpUnit;
        } elseif (preg_match('/^symfony/', $set) === 1) {
            $sets[$set] = $hasSymfony;
        } elseif (preg_match('/^doctrine/', $set) === 1) {
            $sets[$set] = $hasDoctrine;
        } else {
            $sets[$set] = true;
        }
    }
}

$paths = file('php_paths');
if ($paths === false) {
    exit("PHP paths not found\n");
}

$paths = array_map('trim', $paths);

return ECSConfig::configure()
    ->withPaths($paths)
    ->withRootFiles()
    ->withPreparedSets(cleanCode: true, common: true, psr12: true, strict: true, symplify: true)
    ->withPhpCsFixerSets(
        doctrineAnnotation: $sets['doctrineAnnotation'],
        perCS: $sets['perCS'],
        perCS10: $sets['perCS10'],
        perCS10Risky: $sets['perCS10Risky'],
        perCS20: $sets['perCS20'],
        perCS20Risky: $sets['perCS20Risky'],
        perCSRisky: $sets['perCSRisky'],
        perRisky: $sets['perRisky'],
        php54Migration: $sets['php54Migration'],
        php56MigrationRisky: $sets['php56MigrationRisky'],
        php70Migration: $sets['php70Migration'],
        php70MigrationRisky: $sets['php70MigrationRisky'],
        php71Migration: $sets['php71Migration'],
        php71MigrationRisky: $sets['php71MigrationRisky'],
        php73Migration: $sets['php73Migration'],
        php74Migration: $sets['php74Migration'],
        php74MigrationRisky: $sets['php74MigrationRisky'],
        php80Migration: $sets['php80Migration'],
        php80MigrationRisky: $sets['php80MigrationRisky'],
        php81Migration: $sets['php81Migration'],
        php82Migration: $sets['php82Migration'],
        php83Migration: $sets['php83Migration'],
        phpunit30MigrationRisky: $sets['phpunit30MigrationRisky'],
        phpunit32MigrationRisky: $sets['phpunit32MigrationRisky'],
        phpunit35MigrationRisky: $sets['phpunit35MigrationRisky'],
        phpunit43MigrationRisky: $sets['phpunit43MigrationRisky'],
        phpunit48MigrationRisky: $sets['phpunit48MigrationRisky'],
        phpunit50MigrationRisky: $sets['phpunit50MigrationRisky'],
        phpunit52MigrationRisky: $sets['phpunit52MigrationRisky'],
        phpunit54MigrationRisky: $sets['phpunit54MigrationRisky'],
        phpunit55MigrationRisky: $sets['phpunit55MigrationRisky'],
        phpunit56MigrationRisky: $sets['phpunit56MigrationRisky'],
        phpunit57MigrationRisky: $sets['phpunit57MigrationRisky'],
        phpunit60MigrationRisky: $sets['phpunit60MigrationRisky'],
        phpunit75MigrationRisky: $sets['phpunit75MigrationRisky'],
        phpunit84MigrationRisky: $sets['phpunit84MigrationRisky'],
        phpunit100MigrationRisky: $sets['phpunit100MigrationRisky'],
        psr1: $sets['psr1'],
        psr2: $sets['psr2'],
        psr12: $sets['psr12'],
        psr12Risky: $sets['psr12Risky'],
        symfony: $sets['symfony'],
        symfonyRisky: $sets['symfonyRisky'],
    )
    ->withConfiguredRule(
        // Be careful about this part of the config. ECS removes the tag and its
        // contents when what you often want to do is remove or modify the tag
        // only and not its contents.
        GeneralPhpdocAnnotationRemoveFixer::class,
        [
            'annotations' => [
                // Use abstract keyword instead
                'abstract',

                // Use public, protected, or private keyword instead
                'access',

                // Use version history instead
                'author',

                // Use namespaces instead
                'category',

                // Use class keyword instead
                'class',

                // Use @var tag or const keyword instead
                'const',

                // Use constructor keyword instead
                'constructor',

                // Use license file instead
                'copyright',

                // First comment is automatically file comment
                'file',

                // Use final keyword instead
                'final',

                // Use dependency injection instead of globals
                'global',

                // Use @inheritdoc instead
                'inherit',

                // Use license file instead
                'license',

                // Use void return type instead
                'noreturn',

                // Use namespaces instead
                'package',

                // Use @param instead
                'parm',

                // Use private keyword instead
                'private',

                // Use protected keyword instead
                'protected',

                // Use public keyword instead
                'public',

                // Use readonly keyword instead
                'readonly',

                // Use @uses tag instead
                'requires',

                // Use static keyword instead
                'static',

                // Use namespaces instead
                'subpackage',

                // Use type declaration or @var tag instead.
                'type',

                // Use type declaration or @var tag instead.
                'typedef',

                // Use version history instead
                'updated',

                // Use @uses on the other code instead
                'usedby',
            ],
        ]
    );
