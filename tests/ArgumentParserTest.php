<?php

declare(strict_types=1);

namespace DouglasGreen\OptParser\Tests;

use DouglasGreen\OptParser\ArgumentParser;
use PHPUnit\Framework\TestCase;

class ArgumentParserTest extends TestCase
{
    public function testGetMarkedOptions(): void
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $argumentParser = new ArgumentParser($argv);

        $expected = [
            'a' => '',
            'b' => 'arg1',
            'foo' => 'bar',
            'baz' => 'arg2',
        ];

        $this->assertSame($expected, $argumentParser->getMarkedOptions());
    }

    public function testGetNonOptions(): void
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $argumentParser = new ArgumentParser($argv);

        $expected = ['non1', 'non2'];

        $this->assertSame($expected, $argumentParser->getNonOptions());
    }

    public function testGetUnmarkedOptions(): void
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $argumentParser = new ArgumentParser($argv);

        $expected = [];

        $this->assertSame($expected, $argumentParser->getUnmarkedOptions());
    }
}
