<?php

namespace Profounder\Core;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;

abstract class Command extends SymfonyConsoleCommand
{
    /**
     * Command constructor.
     *
     * @param string|null $name Command name.
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
     * Prints app figlet.
     *
     * @param  OutputInterface $output
     *
     * @return void
     */
    protected function outputFiglet(OutputInterface $output)
    {
        $output->writeln("<info>
 ____  ____   ___  _____ ___  _   _ _   _ ____  _____ ____
|  _ \|  _ \ / _ \|  ___/ _ \| | | | \ | |  _ \| ____|  _ \
| |_) | |_) | | | | |_ | | | | | | |  \| | | | |  _| | |_) |
|  __/|  _ <| |_| |  _|| |_| | |_| | |\  | |_| | |___|  _ <
|_|   |_| \_\\___/|_|   \___/ \___/|_| \_|____/|_____|_| \_\
        </>");
    }
}
