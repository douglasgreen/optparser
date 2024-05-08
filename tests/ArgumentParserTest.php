<?php

declare(strict_types=1);

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

    public function testSplitArrayAroundDash(): void
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $others = array_slice($argv, 1);
        $argumentParser = new ArgumentParser($argv);

        $expectedBefore = ['-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2'];
        $expectedAfter = ['non1', 'non2'];

        $result = $this->invokeMethod($argumentParser, 'splitArrayAroundDash', [$others]);

        $this->assertSame($expectedBefore, $result[0]);
        $this->assertSame($expectedAfter, $result[1]);
    }

    public function testJoinArguments(): void
    {
        $argv = ['-abc', '-d', 'arg1', '--foo', 'bar', '--baz=qux'];
        $argumentParser = new ArgumentParser($argv);

        $expected = ['-a', '-b', '-c', '-d=arg1', '--foo=bar', '--baz=qux'];

        $result = $this->invokeMethod($argumentParser, 'joinArguments', [$argv]);

        $this->assertSame($expected, $result);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object     Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method
     *
     * @return mixed method return
     */
    protected function invokeMethod(object $object, string $methodName, array $parameters = [])
    {
        $reflectionClass = new ReflectionClass($object::class);
        $method = $reflectionClass->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
