<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Tests;

use PHPUnit\Framework\TestCase;
use DouglasGreen\OptParser\OptParser;

class OptParserTest extends TestCase
{
    public function testAddCommand(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');

        $this->assertTrue($optParser->optHandler->hasOptionType('command'));
    }

    public function testAddFlag(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addFlag(['verbose', 'v'], 'Enable verbose output');

        $this->assertTrue($optParser->optHandler->hasOptionType('flag'));
    }

    public function testAddParam(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');

        $this->assertTrue($optParser->optHandler->hasOptionType('param'));
    }

    public function testAddTerm(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addTerm('username', 'STRING', 'Username of the user');

        $this->assertTrue($optParser->optHandler->hasOptionType('term'));
    }

    public function testAddUsage(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addUsage(['add', 'username', 'password']);

        $this->assertCount(2, $optParser->usages); // Includes default help usage
    }

    public function testAddUsageAll(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addFlag(['verbose', 'v'], 'Enable verbose output');
        $optParser->addUsageAll();

        $this->assertCount(2, $optParser->usages); // Includes default help usage
    }

    public function testParse(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addUsage(['add', 'username', 'password']);

        $input = $optParser->parse(['test', 'add', 'john', '--password=secret']);

        $this->assertSame('add', $input->getCommand());
        $this->assertSame('john', $input->get('username'));
        $this->assertSame('secret', $input->get('password'));
    }

    public function testParseInvalidCommand(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addUsage(['add', 'username', 'password']);

        $input = $optParser->parse(['test', 'remove', 'john']);

        $this->assertNotNull($input->getErrors());
    }

    public function testHelp(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addFlag(['help', 'h'], 'Display help');

        $this->expectOutputRegex('/Usage:/');
        $optParser->parse(['test', '--help']);
    }
}
