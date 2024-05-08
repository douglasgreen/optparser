<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

// I removed phpCsFixer and phpCsFixerRisky because they conflict with Rector.
$sets = [
    'doctrineAnnotation' => true,
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
    'symfony' => true,
    'symfonyRisky' => false,
];

// To do risky changes, set ECS_RISKY to true in the environment.
$useRisky = (bool) getenv('ECS_RISKY');
if ($useRisky) {
    foreach (array_keys($sets) as $set) {
        $sets[$set] = true;
    }
}

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
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
        GeneralPhpdocAnnotationRemoveFixer::class,
        [
            'annotations' => [
                'arg',
                'author',
                'category',
                'class',
                'const',
                'constructor',
                'copyright',
                'desc',
                'file',
                'group',
                'important',
                'inherit',
                'noreturn',
                'option',
                'package',
                'parm',
                'private',
                'protected',
                'public',
                'purpose',
                'readonly',
                'requires',
                'src',
                'static',
                'subpackage',
                'template',
                'this',
                'type',
                'typedef',
                'updated',
                'usedby',
            ],
        ]
    );
