<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Tests;

use PHPUnit\Framework\TestCase;
use DouglasGreen\Exceptions\ValueException;
use DouglasGreen\OptParser\OptParser;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OptParserTest extends TestCase
{
    public function testAddCommand(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');

        $this->assertTrue($optParser->getOptHandler()->hasOptionType('command'));
    }

    public function testAddFlag(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addFlag(['verbose', 'v'], 'Enable verbose output');

        $this->assertTrue($optParser->getOptHandler()->hasOptionType('flag'));
    }

    public function testAddParam(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');

        $this->assertTrue($optParser->getOptHandler()->hasOptionType('param'));
    }

    public function testAddTerm(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addTerm('username', 'STRING', 'Username of the user');

        $this->assertTrue($optParser->getOptHandler()->hasOptionType('term'));
    }

    public function testAddUsage(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addUsage('add', ['username', 'password']);

        $this->assertCount(2, $optParser->getUsages()); // Includes default help usage
    }

    public function testAddUsageAll(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addFlag(['verbose', 'v'], 'Enable verbose output');
        $optParser->addUsageAll();

        $this->assertCount(2, $optParser->getUsages()); // Includes default help usage
    }

    public function testAddUsageAllEmpty(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addUsageAll();

        $this->assertCount(2, $optParser->getUsages()); // Includes default help usage
    }

    public function testAddUsageMixed(): void
    {
        $optParser = new OptParser('test', 'Test program');

        // Add usage without command.
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addUsageAll();

        // Add usage with command.
        $this->expectException(ValueException::class);
        $optParser->addCommand(['add', 'a'], 'Add a new user');
    }

    public function testAddUsageTwoCommands(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addCommand(['delete', 'd'], 'Delete a user');
        $this->expectException(ValueException::class);
        $optParser->addUsageAll();
    }

    public function testHelp(): void
    {
        $optParser = new OptParser('test', 'Test program', true);
        $optParser->addCommand(['add', 'a'], 'Add a new user');

        $this->expectOutputRegex('/Usage:/');
        $optParser->parse(['test', '--help']);
    }

    public function testParse(): void
    {
        $optParser = new OptParser('test', 'Test program');
        $optParser->addCommand(['add', 'a'], 'Add a new user');
        $optParser->addTerm('username', 'STRING', 'Username of the user');
        $optParser->addParam(['password', 'p'], 'STRING', 'Password for the user');
        $optParser->addUsage('add', ['username', 'password']);

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
        $optParser->addUsage('add', ['username', 'password']);

        $input = $optParser->parse(['test', 'remove', 'john'], false);

        $this->assertNotNull($input->getErrors());
    }
}
