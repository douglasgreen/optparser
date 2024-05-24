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
 * @SuppressWarnings(PHPMD.TooManyMethods)
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

    public function testDateArgType(): void
    {
        $param = new Param('date', 'Date', ['d'], 'DATE');
        $this->assertSame('2024-05-23', $param->matchValue('2024-05-23'));
        $this->assertSame('2024-05-23', $param->matchValue('May 23, 2024'));
        $this->assertSame('2024-05-23', $param->matchValue('23rd May 2024'));
        $this->assertNull($param->matchValue('invalid-date'));
    }

    public function testDatetimeArgType(): void
    {
        $param = new Param('datetime', 'Datetime', ['d'], 'DATETIME');
        $this->assertSame('2024-05-23 15:30:45', $param->matchValue('2024-05-23 15:30:45'));
        $this->assertSame('2024-05-23 15:30:45', $param->matchValue('May 23, 2024 15:30:45'));
        $this->assertSame('2024-05-23 15:30:45', $param->matchValue('23rd May 2024 15:30:45'));
        $this->assertNull($param->matchValue('invalid-datetime'));
    }

    public function testDateIntervalArgType(): void
    {
        $param = new Param('interval', 'Interval', ['i'], 'INTERVAL');
        $this->assertSame('1 year', $param->matchValue('365 days'));
        $this->assertSame('1 year', $param->matchValue('1 year'));
        $this->assertSame('2 years', $param->matchValue('2 years'));
        $this->assertSame('1 month, 1 day', $param->matchValue('1 month'));
        $this->assertSame('2 months, 1 day', $param->matchValue('2 months'));
        $this->assertSame('1 day', $param->matchValue('24 hours'));
        $this->assertSame('2 days', $param->matchValue('2 days'));
        $this->assertSame('1 hour', $param->matchValue('60 minutes'));
        $this->assertSame('2 hours', $param->matchValue('2 hours'));
        $this->assertSame('1 minute', $param->matchValue('60 seconds'));
        $this->assertSame('2 minutes', $param->matchValue('2 minutes'));
        $this->assertSame('10 minutes', $param->matchValue('600 seconds'));
        $this->assertNull($param->matchValue('invalid-datetime'));
    }

    public function testDirType(): void
    {
        $param = new Param('dirname', 'Directory name', ['d'], 'DIR');
        $this->assertNotNull($param->matchValue('tests'));
        $this->assertNull($param->matchValue('/path/to/invalid/directory'));
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

    public function testFixedArgType(): void
    {
        $param = new Param('fixed', 'Fixed', ['f'], 'FIXED');
        $this->assertSame('123.45', $param->matchValue('123.45'));
        $this->assertSame('-123.45', $param->matchValue('-123.45'));
        $this->assertSame('+123.45', $param->matchValue('+123.45'));
        $this->assertSame('0.123', $param->matchValue('0.123'));
        $this->assertSame('123', $param->matchValue('123'));
        $this->assertSame('123456789', $param->matchValue('123,456,789'));
        $this->assertSame('123456789', $param->matchValue('123_456_789'));
        $this->assertNull($param->matchValue('123.45.67'));
        $this->assertNull($param->matchValue('invalid-number'));
    }

    public function testFloatArgType(): void
    {
        $param = new Param('price', 'Item price', ['p'], 'FLOAT');
        $this->assertEqualsWithDelta(19.99, $param->matchValue('19.99'), PHP_FLOAT_EPSILON);
        $this->assertNull($param->matchValue('notafloat'));
    }

    public function testInfileType(): void
    {
        $param = new Param('input', 'Input file name', ['i'], 'INFILE');
        $this->assertNotNull($param->matchValue('composer.json'));
        $this->assertNull($param->matchValue('cat /etc/shadow'));
        $this->assertNull($param->matchValue('/path/to/unreadable/file.txt'));
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

    public function testOutfileType(): void
    {
        $param = new Param('output', 'Output file name', ['o'], 'OUTFILE');
        $this->assertNotNull($param->matchValue('var/file.txt'));
        $this->assertNull($param->matchValue('/path/to/unwritable/directory/file.txt'));
    }

    public function testStringArgType(): void
    {
        $param = new Param('name', 'User name', ['n'], 'STRING');
        $this->assertSame('John Doe', $param->matchValue('John Doe'));
        // Since it's a string type, everything is considered valid for this type
        $this->assertSame('12345', $param->matchValue('12345'));
    }

    public function testTimeArgType(): void
    {
        $param = new Param('time', 'Time', ['t'], 'TIME');
        $this->assertSame('15:30:45', $param->matchValue('15:30:45'));
        $this->assertSame('15:30:00', $param->matchValue('3:30 PM'));
        $this->assertSame('00:00:00', $param->matchValue('midnight'));
        $this->assertNull($param->matchValue('invalid-time'));
    }

    public function testUrlArgType(): void
    {
        $param = new Param('website', 'Website URL', ['w'], 'URL');
        $this->assertSame('https://www.example.com', $param->matchValue('https://www.example.com'));
        $this->assertNull($param->matchValue('invalid-url'));
    }

    public function testUuidArgType(): void
    {
        $param = new Param('uuid', 'UUID', ['u'], 'UUID');

        // Valid UUIDs
        $this->assertSame(
            '123e4567-e89b-12d3-a456-426614174000',
            $param->matchValue('123e4567e89b12d3a456426614174000')
        );
        $this->assertSame(
            '123e4567-e89b-12d3-a456-426614174000',
            $param->matchValue('123e4567-e89b-12d3-a456-426614174000')
        );

        // Invalid UUIDs
        $this->assertNull($param->matchValue('123e4567e89b12d3a45642661417400z')); // Invalid character 'z'
        $this->assertNull($param->matchValue('123e4567e89b12d3a45642661417400'));  // Too short
        $this->assertNull($param->matchValue('123e4567-e89b-12d3-a456-4266141740001')); // Too long
        $this->assertNull($param->matchValue('not-a-uuid')); // Not a UUID format
    }
}
