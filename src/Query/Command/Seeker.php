<?php

namespace Profounder\Query\Command;

use Carbon\Carbon;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Core\Console\ContainerAwareCommand;
use Profounder\Query\Command\Concern\QueryableInputOptions;

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
            ->setName('profounder:query:seek')
            ->setDescription('Runs the profounder command with the configured date range.')
            ->addOption('start', 's', InputOption::VALUE_OPTIONAL, 'Date to start from (Y-m-d).', '1994-01-01')
            ->addOption('end', 'e', InputOption::VALUE_OPTIONAL, 'Seek until this date (Y-m-d).', date('Y-m-d'))
            ->addOption('period', 'p', InputOption::VALUE_OPTIONAL, 'Number of days to increment.', 14)
            ->registerInputOptionsWithoutDate();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputFiglet($output);

        Carbon::setToStringFormat('Y-m-d');

        $options = $input->getOptions();
        $periods = $this->benchmark(function () use ($output, $options) {
            $end     = new Carbon($options['end']);
            $next    = new Carbon($options['start']);
            $command = $this->getApplication()->find($this->queryCommandName);

            $commandInput = [
                'command'  => $this->queryCommandName,
                '--id'     => $options['id'],
                '--sort'   => $options['sort'],
                '--loop'   => $options['loop'],
                '--limit'  => $options['limit'],
                '--order'  => $options['order'],
                '--debug'  => $options['debug'],
                '--delay'  => $options['delay'],
                '--offset' => $options['offset'],
            ];

            $periods = 0;
            while ($next->lessThan($end)) {
                $commandInput['--date'] = "$next," . $next = $next->addDays($options['period']);

                $output->writeln("\n>> Running profounder command with --date={$commandInput['--date']}");

                $command->run($this->make(ArrayInput::class, $commandInput), $output);

                $periods++;
            }

            return $periods;
        }, 'seeker');

        $output->writeln(
            "\nDone! Ran through $periods {$options['period']}-days periods from {$options['start']} to " .
            "{$options['end']} in {$this->elapsed('seeker')}ms"
        );
    }
}
