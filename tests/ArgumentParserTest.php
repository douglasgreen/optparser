<?php

use DouglasGreen\OptParser\ArgumentParser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ArgumentParserTest extends TestCase
{
    public function testGetMarkedOptions()
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $parser = new ArgumentParser($argv);

        $expected = [
            'a' => '',
            'b' => 'arg1',
            'foo' => 'bar',
            'baz' => 'arg2',
        ];

        $this->assertEquals($expected, $parser->getMarkedOptions());
    }

    public function testGetNonOptions()
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $parser = new ArgumentParser($argv);

        $expected = ['non1', 'non2'];

        $this->assertEquals($expected, $parser->getNonOptions());
    }

    public function testGetUnmarkedOptions()
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $parser = new ArgumentParser($argv);

        $expected = [];

        $this->assertEquals($expected, $parser->getUnmarkedOptions());
    }

    public function testSplitArrayAroundDash()
    {
        $argv = ['script.php', '-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2', '--', 'non1', 'non2'];
        $others = array_slice($argv, 1);
        $parser = new ArgumentParser($argv);

        $expectedBefore = ['-a', '-b', 'arg1', '--foo=bar', '--baz', 'arg2'];
        $expectedAfter = ['non1', 'non2'];

        $result = $this->invokeMethod($parser, 'splitArrayAroundDash', [$others]);

        $this->assertEquals($expectedBefore, $result[0]);
        $this->assertEquals($expectedAfter, $result[1]);
    }

    public function testJoinArguments()
    {
        $argv = ['-abc', '-d', 'arg1', '--foo', 'bar', '--baz=qux'];
        $parser = new ArgumentParser($argv);

        $expected = ['-a', '-b', '-c', '-d=arg1', '--foo=bar', '--baz=qux'];

        $result = $this->invokeMethod($parser, 'joinArguments', [$argv]);

        $this->assertEquals($expected, $result);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object     Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod(object $object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
