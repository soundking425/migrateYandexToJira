<?php

namespace App\Command;

use App\Repository\CommentRepository;
use App\Repository\IssuesRepository;
use App\Service\Jira\Issues;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:upload-issues',
    description: 'Add a short description for your command',
)]
class UploadIssuesCommand extends Command
{
    private IssuesRepository $issuesRepository;
    private Issues $issues;
    private CommentRepository $commentRepository;

    public function __construct(
        IssuesRepository $issuesRepository,
        Issues $issues,
        CommentRepository $commentRepository
    )
    {
        parent::__construct();
        $this->issuesRepository = $issuesRepository;
        $this->issues = $issues;
        $this->commentRepository = $commentRepository;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $issues = $this->issuesRepository->findAllSortCreate();
        /**
         * @var \App\Entity\Issues $issue
         */
        foreach ($issues as $issue) {

            if ($issue->getLoadJira() < 1 || empty($issue->getLoadJira())) {
                $data = $this->issues->tackData($issue);
                $res = $this->issues->send($data);

                if (array_key_exists('key', $res)) {
                    $issue->setKeyJira($res['key']);
                    $issue->setIdJira($res['id']);
                    $issue->setLoadJira(1);
                    $this->issuesRepository->saveIssue($issue);
                }else{
                    $io->warning(
                        sprintf(
                            'ERROR LOAD task "%s". ID: %s',
                            $issue->getKey(),
                            $issue->getId()
                        )
                    );
                    dump($data);
                    dd($res);
                }
            }

            $keyJira = $issue->getKeyJira();

            if ($issue->getLoadJira() == 1) {
                $statusJira = $this->issues->getStatusJira($keyJira);
                $updateStatus = $this->issues->updateStatus($keyJira, $issue->getStatus(), $statusJira);
                if ($updateStatus) {
                    $issue->setLoadJira(2);
                    $this->issuesRepository->saveIssue($issue);
                }
            }

            if ($issue->getLoadJira() == 2) {
                if (!empty($issue->getFileLoad())) {
                    foreach ($issue->getFileLoad() as $file) {
                        $this->issues->addAttachments($keyJira, $file);
                    }
                }
                $issue->setLoadJira(3);
                $this->issuesRepository->saveIssue($issue);
            }

            if ($issue->getLoadJira() == 3) {
                $comments = $this->commentRepository->findBy(['issueId' => $issue->getId()]);
                if ($comments) {
                    foreach ($comments as $comment) {
                        if (!empty($comment->getFileLoad())) {
                            foreach ($comment->getFileLoad() as $file) {
                                $resFile = $this->issues->addAttachments($keyJira, $file);
                            }
                        }

                        $this->issues->addComment($keyJira, $comment, $comment->getFileLoad());
                    }
                }
                $issue->setLoadJira(4);
                $this->issuesRepository->saveIssue($issue);
            }

            $io->note(
                sprintf(
                    'LOAD task "%s" JIRA: "%s". ID: %s',
                    $issue->getKey(),
                    $keyJira,
                    $issue->getId()
                )
            );
        }

        return Command::SUCCESS;
    }

}
