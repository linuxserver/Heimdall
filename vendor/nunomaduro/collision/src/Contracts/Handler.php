<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts;

use Symfony\Component\Console\Output\OutputInterface;
use Whoops\Handler\HandlerInterface;

/**
 * @internal
 */
interface Handler extends HandlerInterface
{
    /**
     * Sets the output.
     *
     * @return \NunoMaduro\Collision\Contracts\Handler
     */
    public function setOutput(OutputInterface $output): Handler;

    /**
     * Returns the writer.
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    public function getWriter(): Writer;
}
