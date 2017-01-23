<?php

namespace Profounder\Commands;

use Carbon\Carbon;
use Profounder\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seeker extends ContainerAwareCommand
{
    /**
     * Query command name.
     *
     * @var string
     */
    private $queryCommandName = 'profounder:query';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:seek')
            ->setDescription('Runs the profounder command with the configured date range.')
            ->addOption('start', 's', InputOption::VALUE_OPTIONAL, 'Date to start from (Y-m-d).', '1994-01-01')
            ->addOption('end', 'e', InputOption::VALUE_OPTIONAL, 'Seek until this date (Y-m-d).', date('Y-m-d'))
            ->addOption('period', 'p', InputOption::VALUE_OPTIONAL, 'Number of days to increment.', 14)
            ->addOption('order', 'r', InputOption::VALUE_OPTIONAL, 'Date sort order.', 'desc');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Carbon::setToStringFormat('Y-m-d');

        $command = $this->getApplication()->find($this->queryCommandName);
        $start   = $next = new Carbon($input->getOption('start'));
        $end     = new Carbon($input->getOption('end'));

        $commandInput = [
            'command'  => $this->queryCommandName,
            '--id'     => 3,
            '--offset' => 0,
            '--chunk'  => 4019,
            '--loop'   => 1,
            '--sort'   => 'docdatetime',
            '--order'  => $input->getOption('order'),
        ];

        $periods = 0;
        $period  = $input->getOption('period');

        while ($next->lessThan($end)) {
            $commandInput['--date'] = "$next," . $next = $next->addDays($period);

            $output->writeln("\n>> Running profounder command with --date={$commandInput['--date']}");

            $command->run(new ArrayInput($commandInput), $output);

            $periods++;
        }

        $output->writeln("\nDone! Ran through $periods $period-days periods from $start to $end.");
    }
}
