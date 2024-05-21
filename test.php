<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use DouglasGreen\OptParser\OptParser;

/*
$optParser = new OptParser($argv, 'Test', 'My testing program');

// @todo Change types to validated string types like EMAIL or URL
// @todo Add another t/f/p/c argument for a function call to validate/filter
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
    ->addUsage(['stop', 'timeout']);

// @todo Replace with help usage.
echo $optParser->writeHelp();

*/
$optParser = new OptParser($argv, 'My Program', 'An example program');

$optParser->addParam(['f', 'file'], 'STRING', 'File name')
    ->addUsageAll();

$usage = $optParser->matchUsage();

var_dump($usage);
