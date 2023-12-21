<?php

namespace App\Command;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\IssuesRepository;
use App\Repository\QueuesRepository;
use App\Service\YandexTracker\Issues;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\UnableToWriteFile;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-file',
    description: 'Add a short description for your command',
)]
class LoadFileCommand extends Command
{
    private QueuesRepository $queuesRepository;
    private IssuesRepository $issuesRepository;
    private CommentRepository $commentRepository;
    private Issues $issues;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Issues $issues,
        QueuesRepository $queuesRepository,
        IssuesRepository $issuesRepository,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
        $this->queuesRepository = $queuesRepository;
        $this->issuesRepository = $issuesRepository;
        $this->commentRepository = $commentRepository;
        $this->issues = $issues;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $issues = $this->issuesRepository->findAttachments();
        /**
         * @var \App\Entity\Issues $issue
         */
        foreach ($issues as $issue) {
            $fileLoad = [];
            foreach ($issue->getAttachments() as $attachment) {
                $structure = 'public/issues/' . $issue->getKey() . '/' . $attachment['id'];
                $fileLoad[] = $this->issues->fileDownload($issue->getKey(), $attachment['id'], $attachment['display'], $structure);

            }

            $issue->setFileLoad($fileLoad);
            $this->entityManager->persist($issue);
            $this->entityManager->flush();

            $io->note(
                sprintf(
                    'LOAD File task "%s". ID: %s',
                    $issue->getKey(),
                    $issue->getId()
                )
            );
        }


        /**
         * @var Comment $comment
         */
        $comments = $this->commentRepository->findAttachments();
        foreach ($comments as $comment) {
            $fileLoad = [];
            foreach ($comment->getFile() as $files) {
                $structure = 'public/comments/' . $comment->getIssues() . '/' . $files['id'];
                $fileLoad[] = $this->issues->fileDownload($comment->getIssues(), $files['id'], $files['display'], $structure);

            }

            $comment->setFileLoad($fileLoad);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $io->note(
                sprintf(
                    'LOAD File task "%s". comment ID: %s',
                    $comment->getIssues(),
                    $comment->getId()
                )
            );
        }

        $io->success('COMPLETED!');

        return Command::SUCCESS;
    }
}
