<?php

namespace Profounder\Commands;

use Carbon\Carbon;
use Profounder\Benchmarkable;
use Profounder\ContainerAwareCommand;
use Profounder\Query\QueryableInputOptions;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Seeker extends ContainerAwareCommand
{
    use Benchmarkable, QueryableInputOptions;

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
            ->registerQueryInputOptionsWithoutDate();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputFiglet($output);

        Carbon::setToStringFormat('Y-m-d');

        list($periods, $period, $start, $end) = $this->benchmark(function () use ($input, $output) {
            $command = $this->getApplication()->find($this->queryCommandName);
            $start   = $next = new Carbon($input->getOption('start'));
            $end     = new Carbon($input->getOption('end'));

            $commandInput = [
                'command'  => $this->queryCommandName,
                '--id'     => $input->getOption('id'),
                '--sort'   => $input->getOption('sort'),
                '--loop'   => $input->getOption('loop'),
                '--limit'  => $input->getOption('limit'),
                '--order'  => $input->getOption('order'),
                '--offset' => $input->getOption('offset'),
            ];

            $periods = 0;
            $period  = $input->getOption('period');

            while ($next->lessThan($end)) {
                $commandInput['--date'] = "$next," . $next = $next->addDays($period);

                $output->writeln("\n>> Running profounder command with --date={$commandInput['--date']}");

                $command->run(new ArrayInput($commandInput), $output);

                $periods++;
            }

            return [$periods, $period, $start, $end];
        }, 'seeker');

        $output->writeln(
            "\nDone! Ran through $periods $period-days periods from $start to $end in {$this->elapsed('seeker')}ms."
        );
    }
}
