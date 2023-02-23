<?php

namespace pjpawel\LightApi\Command\Output;

interface OutputInterface
{

    /**
     * Write the text. In this method EOL mark is NOT added
     *
     * @param string $text
     * @return void
     */
    public function write(string $text): void;

    /**
     * Write lines separated with EOL mark. It will be added at the end of the text as well.
     *
     * @param array|string $text
     * @return void
     */
    public function writeln(array|string $text): void;

}