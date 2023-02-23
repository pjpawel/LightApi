<?php

namespace pjpawel\LightApi\Command\Output;

class Stdout implements OutputInterface
{

    /**
     * @inheritDoc
     */
    public function write(string $text): void
    {
        echo $text;
    }

    /**
     * @inheritDoc
     */
    public function writeln(array|string $text): void
    {
        if (is_array($text)) {
            echo implode(PHP_EOL, $text) . PHP_EOL;
        } else {
            echo $text . PHP_EOL;
        }
    }
}