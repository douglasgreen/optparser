<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Tests;

use DouglasGreen\OptParser\OptHandler;
use DouglasGreen\Utility\Exceptions\Data\ValueException;
use PHPUnit\Framework\TestCase;

class OptHandlerTest extends TestCase
{
    public function testAddCommand(): void
    {
        $optHandler = new OptHandler();
        $optHandler->addCommand(['add', 'a'], 'Add a new user');

        $this->assertTrue($optHandler->hasOptionType('command'));
        $command = $optHandler->getOption('add');
        $this->assertSame('add', $command->getName());
        $this->assertSame('Add a new user', $command->getDesc());
        $this->assertSame(['a'], $command->getAliases());
    }

    public function testAddFlag(): void
    {
        $optHandler = new OptHandler();
        $optHandler->addFlag(['verbose', 'v'], 'Enable verbose output');

        $this->assertTrue($optHandler->hasOptionType('flag'));
        $flag = $optHandler->getOption('verbose');
        $this->assertSame('verbose', $flag->getName());
        $this->assertSame('Enable verbose output', $flag->getDesc());
        $this->assertSame(['v'], $flag->getAliases());
    }

    public function testAddParam(): void
    {
        $optHandler = new OptHandler();
        $optHandler->addParam(
            ['password', 'p'],
            'STRING',
            'Password for the user',
        );

        $this->assertTrue($optHandler->hasOptionType('param'));
        $param = $optHandler->getOption('password');
        $this->assertSame('password', $param->getName());
        $this->assertSame('Password for the user', $param->getDesc());
        $this->assertSame(['p'], $param->getAliases());
        $this->assertSame('STRING', $param->getArgType());
    }

    public function testAddTerm(): void
    {
        $optHandler = new OptHandler();
        $optHandler->addTerm('username', 'STRING', 'Username of the user');

        $this->assertTrue($optHandler->hasOptionType('term'));
        $term = $optHandler->getOption('username');
        $this->assertSame('username', $term->getName());
        $this->assertSame('Username of the user', $term->getDesc());
        $this->assertSame('STRING', $term->getArgType());
    }

    public function testDuplicateAliasException(): void
    {
        $this->expectException(ValueException::class);

        $optHandler = new OptHandler();
        $optHandler->addFlag(['verbose', 'v'], 'Enable verbose output');
        $optHandler->addFlag(['verbose', 'v'], 'Another verbose flag');
    }

    public function testGetOptionType(): void
    {
        $optHandler = new OptHandler();
        $optHandler->addCommand(['add', 'a'], 'Add a new user');
        $optHandler->addFlag(['verbose', 'v'], 'Enable verbose output');
        $optHandler->addParam(
            ['password', 'p'],
            'STRING',
            'Password for the user',
        );
        $optHandler->addTerm('username', 'STRING', 'Username of the user');

        $this->assertSame('command', $optHandler->getOptionType('add'));
        $this->assertSame('flag', $optHandler->getOptionType('verbose'));
        $this->assertSame('param', $optHandler->getOptionType('password'));
        $this->assertSame('term', $optHandler->getOptionType('username'));
    }

    public function testInvalidOptionTypeException(): void
    {
        $this->expectException(ValueException::class);

        $optHandler = new OptHandler();
        $optHandler->getOptionType('nonexistent');
    }

    public function testWriteOptionBlock(): void
    {
        $optHandler = new OptHandler();
        $optHandler->addCommand(['add', 'a'], 'Add a new user');
        $optHandler->addFlag(['verbose', 'v'], 'Enable verbose output');
        $optHandler->addParam(
            ['password', 'p'],
            'STRING',
            'Password for the user',
        );
        $optHandler->addTerm('username', 'STRING', 'Username of the user');

        $output = $optHandler->writeOptionBlock();

        $this->assertStringContainsString('Commands:', $output);
        $this->assertStringContainsString('add | a', $output);
        $this->assertStringContainsString('Flags:', $output);
        $this->assertStringContainsString('--verbose | -v', $output);
        $this->assertStringContainsString('Parameters:', $output);
        $this->assertStringContainsString('--password | -p = STRING', $output);
        $this->assertStringContainsString('Terms:', $output);
        $this->assertStringContainsString('username: STRING', $output);
    }
}
