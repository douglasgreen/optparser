<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use DouglasGreen\OptParser\OptParser;

$optParser = new OptParser($argv, 'Test', 'My testing program');

$optParser->addCommand(['run'], 'Run it')
    ->addCommand(['stop'], 'Stop it')

    ->addTerm('command', 'string', 'Command to execute')
    ->addTerm('arguments', 'string', 'Additional arguments')

    ->addFlag(['v', 'verbose'], 'Enable verbose output')
    ->addFlag(['f', 'force'], 'Force operation')
    ->addFlag(['q', 'quiet'], 'Suppress output')
    ->addFlag(['d', 'debug'], 'Enable debug mode')

    ->addParam(['o', 'output'], 'STRING', 'Output file')
    ->addParam(['c', 'config'], 'STRING', 'Configuration file')
    ->addParam(['l', 'log'], 'STRING', 'Log file')
    ->addParam(['t', 'timeout'], 'INT', 'Timeout in seconds')

    ->addUsage(['run', 'verbose'])
    ->addUsage(['stop', 'timeout'])
    ->addUsageAll();

// @todo Replace with help usage.
echo $optParser->writeHelpBlock();
