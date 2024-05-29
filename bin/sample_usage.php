#!/usr/bin/env php
<?php

declare(strict_types=1);

use DouglasGreen\Exceptions\BadArgumentException;
use DouglasGreen\OptParser\OptParser;

require_once __DIR__ . '/../vendor/autoload.php';

// Define program
$optParser = new OptParser('User Manager', 'A program to manage user accounts');

// Adding commands
$optParser->addCommand(['add', 'a'], 'Add a new user')
    ->addCommand(['delete', 'd'], 'Delete an existing user')
    ->addCommand(['list', 'l'], 'List all users')

    // Adding terms
    ->addTerm('username', 'STRING', 'Username of the user')
    ->addTerm('email', 'STRING', 'Email of the user')

    // Adding flags
    ->addFlag(['v', 'verbose'], 'Enable verbose output')
    ->addFlag(['q', 'quiet'], 'Suppress output')

    // Adding parameters
    ->addParam(['p', 'password'], 'STRING', 'Password for the user')
    ->addParam(
        ['r', 'role'],
        'string',
        'Role of the user',
        // An example callback function that limits role to enumerated values.
        // Throw an BadArgumentException on failure to describe the reason.
        static function ($role): string {
            if (in_array($role, ['admin', 'manager', 'user'], true)) {
                return $role;
            }

            throw new BadArgumentException('Role must be admin, manager, or user');
        }
    )
    ->addParam(['o', 'output'], 'OUTFILE', 'Output file for the list command')

    // Adding usage examples
    ->addUsage(['add', 'username', 'email', 'password', 'role'])
    ->addUsage(['delete', 'username'])
    ->addUsage(['list', 'output', 'verbose']);

// Matching usage
$input = $optParser->parse();

// Get command executed
$command = $input->getCommand();

// Debugging output
switch ($command) {
    case 'add':
        var_dump($input->get('username'));
        var_dump($input->get('email'));
        var_dump($input->get('password'));
        var_dump($input->get('role'));
        break;
    case 'remove':
        var_dump($input->get('username'));
        break;
    case 'list':
        var_dump($input->get('output'));
        var_dump($input->get('verbose'));
        break;
}
