<?php

namespace App\Service\YandexTracker;

use App\Entity\Queues;
use App\Repository\IssuesRepository;
use App\Repository\QueuesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Issues
{
    private $token;
    private $orgId;

    private $url = 'https://api.tracker.yandex.net';

    private HttpClientInterface $client;
    private QueuesRepository $queuesRepository;
    private IssuesRepository $issuesRepository;

    public function __construct(
        HttpClientInterface $client,
        Token $token,
        QueuesRepository $queuesRepository,
        IssuesRepository $issuesRepository,
        $orgIdYandex
    )
    {
        $this->token = $token->getToken();
        $this->orgId = $orgIdYandex;
        $this->client = $client;
        $this->queuesRepository = $queuesRepository;
        $this->issuesRepository = $issuesRepository;
    }

    public function count($queue)
    {
        $response = $this->client->request('POST', $this->url . '/v2/issues/_count', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'X-Org-ID' => $this->orgId,
            ],
            'json' => [
                'filter' => [
                    "queue" => $queue,
                ]
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            $count = $response->getContent();
            $pageCount = intdiv($count, 50);
            if ($count % 50) {
                $pageCount++;
            }
            return $pageCount;
        }

        return $response->getInfo();
    }

    public function comments($issueId, $commentId = '')
    {
        $param = '';
        if ($commentId != '') {
            $param = '&id=' . $commentId;
        }
        $response = $this->client->request('GET', $this->url . '/v2/issues/' . $issueId . '/comments?expand=all&perPage=20' . $param, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'X-Org-ID' => $this->orgId
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return $response->toArray();
        }

        return $response->getInfo();
    }

    public function files($issueId)
    {
        $response = $this->client->request('GET', $this->url . '/v2/issues/' . $issueId . '/attachments', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'X-Org-ID' => $this->orgId
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return $response->getContent();
        }

        return $response->getInfo();
    }

    public function fileDownload($issueId, $attachmentId, $filename, $structure)
    {
//        $response = $this->client->request('GET', $this->url . '/v2/issues/' . $issueId . '/attachments/' . $attachmentId . '/' . $filename, [
//            'headers' => [
//                'Authorization' => 'Bearer ' . $this->token,
//                'Content-Type' => 'application/octet-stream',
//                'X-Org-ID' => $this->orgId
//            ],
//        ]);
//
//        $statusCode = $response->getStatusCode();
//        if ($statusCode == 200) {
//            $fileName = $structure . '/' . $filename;
//            $stream = $response->getContent();
//            file_put_contents(
//                $fileName,
//                $stream,
//                FILE_APPEND
//            );
//            return $fileName;
//        }
//
//        if ($statusCode == 104) {
//
//        }
//
//        return $response->getInfo();

        $context = stream_context_create([
            'http' => [
                'method' => "GET",
                'header' => 'Authorization: Bearer ' . $this->token . "\r\n" .
                    'X-Org-ID: ' . $this->orgId . "\r\n" .
                    'Content-Type: ' . 'application/octet-stream'
            ]
        ]);

        if (!is_dir($structure)) {
            mkdir($structure, 0777, true);
        }

        $url = $this->url . '/v2/issues/' . $issueId . '/attachments/' . $attachmentId . '/' . urlencode($filename);

        $stream = fopen(
            $url,
            'r',
            false,
            $context
        );


        $arFileName = explode('.', $filename);
        $arFileName[0] = mb_strimwidth($arFileName[0], 0, 100, "...");
        $newFileName = implode('.', $arFileName);

        $fileNameAll = $structure . '/' . $newFileName;

        @file_put_contents(
            $fileNameAll,
            $stream,
            LOCK_EX
        );

        fpassthru($stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $fileNameAll;
    }

    public function issues($queue, $page = 1)
    {
        $response = $this->client->request('POST', $this->url . '/v2/issues/_search?expand=attachments,html&perPage=50&page=' . $page, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'X-Org-ID' => $this->orgId,
            ],
            'json' => [
                "filter" => [
                    "queue" => $queue
                ]
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return $response->toArray();
        }

        return $response->getInfo();
    }

    public function queues()
    {
        $response = $this->client->request('GET', $this->url . '/v2/queues/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'X-Org-ID' => $this->orgId
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return $response->toArray();
        }

        return $response->getInfo();
    }

    public function loadQueues()
    {
        $queues = $this->queues();
        foreach ($queues as $queue) {
            $pageCount = $this->count($queue['key']);
            $queueLoads = $this->queuesRepository->findOneBy(['key' => $queue['key']]);
            if (empty($queueLoads)) {
                $this->queuesRepository->create($queue['name'], $queue['key'], $pageCount);
            }else{
                $queueLoads
                    ->setPageCount($pageCount)
                    ->setCurrentPage(1)
                ;

                $this->queuesRepository->save($queueLoads);
            }
        }

        return [
            'queues' => $queues,
        ];
    }

    public function loadIssues($queueKey, $page)
    {
        $yandexIssues = $this->issues($queueKey, $page);
        foreach ($yandexIssues as $yandexIssue) {
            $issue = $this->issuesRepository->findOneBy(['key' => $yandexIssue['key']]);
            if ($issue) {
                $issue
                    ->setDescription($yandexIssue['description'] ?? '')
                    ->setDescriptionHtml($yandexIssue['descriptionHtml'] ?? '')
                    ->setType($yandexIssue['type'] ?? null)
                    ->setEpic($yandexIssue['epic']['key'] ?? null)
                    ->setComponents($yandexIssue['components'] ?? null)
                    ->setProject($yandexIssue['queue']['key'])
                    ->setTitle($yandexIssue['summary'])
                    ->setStatus($yandexIssue['status']['display'])
                    ->setParent($yandexIssue['parent']['key'] ?? null)
                    ->setPriority($yandexIssue['priority'])
                    ->setQueue($yandexIssue['queue'])
                    ->setAttachments($yandexIssue['attachments'] ?? null)
                    ->setBoards($yandexIssue['boards']['name'] ?? null)
                ;
                $this->issuesRepository->saveIssue($issue);
            } else {
                $this->issuesRepository->create($yandexIssue);
            }
        }

        return [
            'queueKey' => $queueKey,
            'issues' => $yandexIssues
        ];
    }
}