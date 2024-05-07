<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use DouglasGreen\OptParser\ArgumentParser;
use DouglasGreen\OptParser\OptionHandler;
use DouglasGreen\OptParser\Program;
use DouglasGreen\OptParser\Usage;

$argParser = new ArgumentParser($argv);

$optHandler = new OptionHandler();

$program = new Program($argParser, $optHandler, 'Test', 'My testing program');

$optHandler->addCommand(['run'], 'Run it');
$optHandler->addCommand(['stop'], 'Stop it');

$optHandler->addFlag(['v', 'verbose'], 'Enable verbose output');
$optHandler->addFlag(['f', 'force'], 'Force operation');
$optHandler->addFlag(['q', 'quiet'], 'Suppress output');
$optHandler->addFlag(['d', 'debug'], 'Enable debug mode');

$optHandler->addParam(['o', 'output'], 'STRING', 'Output file');
$optHandler->addParam(['c', 'config'], 'STRING', 'Configuration file');
$optHandler->addParam(['l', 'log'], 'STRING', 'Log file');
$optHandler->addParam(['t', 'timeout'], 'INT', 'Timeout in seconds');

$optHandler->addTerm('command', 'string', 'Command to execute');
$optHandler->addTerm('arguments', 'string', 'Additional arguments');

$runUsage = new Usage($optHandler);
$runUsage->addOption('run', true);
$runUsage->addOption('verbose');

$stopUsage = new Usage($optHandler);
$stopUsage->addOption('stop', true);
$stopUsage->addOption('timeout');

$program->addUsage($runUsage);
$program->addUsage($stopUsage);
echo $program->writeHelpBlock();
