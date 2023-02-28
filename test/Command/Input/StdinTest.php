<?php

namespace pjpawel\LightApi\Test\Command\Input;

use pjpawel\LightApi\Command\Input\Argument;
use pjpawel\LightApi\Command\Input\Option;
use pjpawel\LightApi\Command\Input\Stdin;
use PHPUnit\Framework\TestCase;

/**
 * @covers \pjpawel\LightApi\Command\Input\Stdin
 */
class StdinTest extends TestCase
{

    private function getStdin(): Stdin
    {
        $stdin = new Stdin();
        $stdin->addArgument('first_arg');
        $stdin->addArgument('sec_arg');
        $stdin->addArgument('third_arg', Argument::OPTIONAL);
        $stdin->addOption('help', 'h');
        $stdin->addOption('port', 'p', default: 5000);
        return $stdin;
    }

    /**
     * @param Argument[]|Option[] $args
     * @return int
     */
    private function countValuesOfArgs(array $args): int
    {
        $counter = 0;
        foreach ($args as $arg) {
            if (isset($arg->value)) {
                $counter++;
            }
        }
        return $counter;
    }

    private function assertValuesLoaded(Stdin $stdin, int $expectedArgs, int $expectedOpts): void
    {
        $stdin->load();
        $this->assertEquals($expectedArgs, $this->countValuesOfArgs($stdin->arguments));
        $this->assertEquals($expectedOpts, $this->countValuesOfArgs($stdin->options));
    }

    /**
     * @covers \pjpawel\LightApi\Command\Input\Stdin::load
     */
    public function testLoad(): void
    {
        $stdin = $this->getStdin();
        $this->assertCount(3, $stdin->arguments);
        $this->assertCount(2, $stdin->options);

        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf'
        ];
        $this->assertValuesLoaded($stdin, 2, 0);

        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf',
            'bsfg'
        ];
        $this->assertValuesLoaded($stdin, 3, 0);

        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf',
            '-p=gfd'
        ];
        $this->assertValuesLoaded($stdin, 2, 1);

        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf',
            '-p',
            'gdf'
        ];
        $this->assertValuesLoaded($stdin, 2, 1);
    }

    /**
     * @covers \pjpawel\LightApi\Command\Input\Stdin::getArgument
     */
    public function testGetArgument(): void
    {
        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf',
        ];
        $stdin = $this->getStdin();
        $stdin->load();

        $this->assertEquals('abc', $stdin->getArgument('first_arg'));
        $this->assertEquals('sdf', $stdin->getArgument('sec_arg'));
        $this->assertEquals(null, $stdin->getArgument('third_arg'));
    }

    /**
     * @covers \pjpawel\LightApi\Command\Input\Stdin::getOption
     */
    public function testGetOption(): void
    {
        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf',
        ];
        $stdin = $this->getStdin();
        $stdin->load();

        $this->assertEquals(null, $stdin->getOption('help'));
        $this->assertEquals(5000, $stdin->getOption('port'));

        $_SERVER['argv'] = [
            'some_script.php',
            'command:name',
            'abc',
            'sdf',
            '-p=4000',
            '-h',
            'abdfg'
        ];
        $stdin = $this->getStdin();
        $stdin->load();

        $this->assertEquals('abdfg', $stdin->getOption('help'));
        $this->assertEquals('4000', $stdin->getOption('port'));
    }
}
