<?php

namespace App\Command;

use App\Repository\CommentRepository;
use App\Repository\IssuesRepository;
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
    name: 'app:load-comment',
    description: 'Add a short description for your command',
)]
class LoadCommentCommand extends Command
{
    private QueuesRepository $queuesRepository;
    private IssuesRepository $issuesRepository;
    private CommentRepository $commentRepository;
    private Issues $issues;

    public function __construct(
        Issues $issues,
        QueuesRepository $queuesRepository,
        IssuesRepository $issuesRepository,
        CommentRepository $commentRepository
    )
    {
        parent::__construct();
        $this->queuesRepository = $queuesRepository;
        $this->issuesRepository = $issuesRepository;
        $this->commentRepository = $commentRepository;
        $this->issues = $issues;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $issues = $this->issuesRepository->findAll();

        foreach ($issues as $issue) {

            $commentLoad = false;
            $lastCommentID = '';
            while ($commentLoad == false) {
                $comments = $this->issues->comments($issue->getKey(), $lastCommentID);

                foreach ($comments as $comment) {
                    $this->commentRepository->create($comment, $issue->getKey(), $issue->getId());
                    $lastCommentID = $comment['id'];
                }

                if (empty($comments)) {
                    $commentLoad = true;
                }
            }


            $io->note(
                sprintf(
                    'LOAD Comment task "%s". ID: %s',
                    $issue->getKey(),
                    $issue->getId()
                )
            );
        }

        $io->success('COMPLETED!');

        return Command::SUCCESS;
    }
}
