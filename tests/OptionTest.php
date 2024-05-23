<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Tests;

use PHPUnit\Framework\TestCase;
use DouglasGreen\OptParser\Option\Command;
use DouglasGreen\OptParser\Option\Term;
use DouglasGreen\OptParser\Option\Param;
use DouglasGreen\OptParser\Option\Flag;
use DouglasGreen\OptParser\OptParserException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OptionTest extends TestCase
{
    public function testCommandCreation(): void
    {
        $command = new Command('add', 'Add a new user', ['a']);
        $this->assertSame('add', $command->getName());
        $this->assertSame('Add a new user', $command->getDesc());
        $this->assertSame(['a'], $command->getAliases());
        $this->assertSame('add', $command->write());
    }

    public function testTermCreation(): void
    {
        $term = new Term('username', 'Username of the user', 'STRING');
        $this->assertSame('username', $term->getName());
        $this->assertSame('Username of the user', $term->getDesc());
        $this->assertSame('STRING', $term->getArgType());
        $this->assertSame('username:STRING', $term->write());
    }

    public function testParamCreation(): void
    {
        $param = new Param('password', 'Password for the user', ['p'], 'STRING');
        $this->assertSame('password', $param->getName());
        $this->assertSame('Password for the user', $param->getDesc());
        $this->assertSame(['p'], $param->getAliases());
        $this->assertSame('STRING', $param->getArgType());
        $this->assertSame('--password=STRING', $param->write());
    }

    public function testFlagCreation(): void
    {
        $flag = new Flag('help', 'Display program help', ['h']);
        $this->assertSame('help', $flag->getName());
        $this->assertSame('Display program help', $flag->getDesc());
        $this->assertSame(['h'], $flag->getAliases());
        $this->assertSame('--help', $flag->write());
    }

    public function testInvalidAlias(): void
    {
        $this->expectException(OptParserException::class);
        new Command('invalid_command', 'Invalid Command', ['bad_alias']);
    }

    public function testOptionMatchName(): void
    {
        $flag = new Flag('help', 'Display program help', ['h']);
        $this->assertTrue($flag->matchName('help'));
        $this->assertTrue($flag->matchName('h'));
        $this->assertFalse($flag->matchName('x'));
    }

    public function testOptionMatchValue(): void
    {
        $param = new Param('email', 'User email', ['e'], 'EMAIL');
        $this->assertSame('test@example.com', $param->matchValue('test@example.com'));
        $this->assertNull($param->matchValue('invalid-email'));
    }

    public function testOptionInvalidType(): void
    {
        $this->expectException(OptParserException::class);
        new Param('invalid', 'Invalid Param', ['i'], 'INVALID_TYPE');
    }

    public function testBoolArgType(): void
    {
        $param = new Param('active', 'Is active', ['a'], 'BOOL');
        $this->assertTrue($param->matchValue('true'));
        $this->assertTrue($param->matchValue('1'));
        $this->assertFalse($param->matchValue('false'));
        $this->assertFalse($param->matchValue('0'));
        $this->assertNull($param->matchValue('notabool'));
    }

    public function testDomainArgType(): void
    {
        $param = new Param('domain', 'Domain name', ['d'], 'DOMAIN');
        $this->assertSame('example.com', $param->matchValue('example.com'));
        $this->assertNull($param->matchValue('invalid-.domain'));
    }

    public function testEmailArgType(): void
    {
        $param = new Param('email', 'User email', ['e'], 'EMAIL');
        $this->assertSame('test@example.com', $param->matchValue('test@example.com'));
        $this->assertNull($param->matchValue('invalid-email'));
    }

    public function testFloatArgType(): void
    {
        $param = new Param('price', 'Item price', ['p'], 'FLOAT');
        $this->assertEqualsWithDelta(19.99, $param->matchValue('19.99'), PHP_FLOAT_EPSILON);
        $this->assertNull($param->matchValue('notafloat'));
    }

    public function testIntArgType(): void
    {
        $param = new Param('age', 'User age', ['a'], 'INT');
        $this->assertSame(25, $param->matchValue('25'));
        $this->assertNull($param->matchValue('notanint'));
    }

    public function testIpAddrArgType(): void
    {
        $param = new Param('ip', 'IP address', ['i'], 'IP_ADDR');
        $this->assertSame('192.168.1.1', $param->matchValue('192.168.1.1'));
        $this->assertNull($param->matchValue('notanip'));
    }

    public function testMacAddrArgType(): void
    {
        $param = new Param('mac', 'MAC address', ['m'], 'MAC_ADDR');
        $this->assertSame('00:1A:2B:3C:4D:5E', $param->matchValue('00:1A:2B:3C:4D:5E'));
        $this->assertNull($param->matchValue('notamac'));
    }

    public function testStringArgType(): void
    {
        $param = new Param('name', 'User name', ['n'], 'STRING');
        $this->assertSame('John Doe', $param->matchValue('John Doe'));
        // Since it's a string type, everything is considered valid for this type
        $this->assertSame('12345', $param->matchValue('12345'));
    }

    public function testUrlArgType(): void
    {
        $param = new Param('website', 'Website URL', ['w'], 'URL');
        $this->assertSame('https://www.example.com', $param->matchValue('https://www.example.com'));
        $this->assertNull($param->matchValue('invalid-url'));
    }
}