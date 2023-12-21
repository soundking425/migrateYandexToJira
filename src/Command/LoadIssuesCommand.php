<?php

namespace App\Command;

use App\Repository\QueuesRepository;
use App\Service\YandexTracker\Issues;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-issues',
    description: 'Add a short description for your command',
)]
class LoadIssuesCommand extends Command
{
    private Issues $issues;
    private QueuesRepository $queuesRepository;

    public function __construct(Issues $issues, QueuesRepository $queuesRepository)
    {
        parent::__construct();
        $this->issues = $issues;
        $this->queuesRepository = $queuesRepository;
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $queues = $this->issues->loadQueues();

        $bdQueues = $this->queuesRepository->findAll();

        foreach ($bdQueues as $queue) {
            $pageCount = $queue->getPageCount();
            for ($i = 1; $i <= $pageCount; $i++) {
                $this->issues->loadIssues($queue->getKey(), $i);
                $io->note(
                    sprintf(
                        'LOAD Project "%s": %s/%s',
                    $queue->getKey(),
                        $queue->getCurrentPage()*50,
                        $queue->getPageCount()*50,
                    )
                );
                $this->queuesRepository->upPage($queue);
            }
        }

        $io->success('COMPLETED!');

        return Command::SUCCESS;
    }
}
