<?php

namespace App\Service\Jira;

use App\Entity\Comment;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Issues
{
    private $token;
    private $url = 'https://jira.kingbird.ru';
    private HttpClientInterface $client;

    public function __construct($tokenJira, HttpClientInterface $client)
    {
        $this->token = $tokenJira;
        $this->client = $client;
    }

    public function tackData(\App\Entity\Issues $issue)
    {
        $types = [
            'epic' => '10000',
            'task' => '10002',
            'bug' => '10100'
        ];
        $type = $types['task'];
        if (array_key_exists($issue->getType()['key'], $types)) {
            $type = $types[$issue->getType()['key']];
        }
        $components = [];
        if (!empty($issue->getComponents())) {
            foreach ($issue->getComponents() as $component){
                $components[] = ["name" => $component['display']];
            }
        }
        $data = [
            'fields' => [
                'project' => [
                    'key' => $issue->getProject()
                ],
                'summary' => $issue->getTitle(),
                'description' => '[' . $issue->getKey() . "] \n\n" . $issue->getDescription(),
                'issuetype' => [
                    'id' => $type
                ],
                'priority' => [
                    'id' => '3'
                ]
            ]
        ];
        if ($issue->getType()['key'] == 'epic') {
            $data['fields']['customfield_10104'] = $issue->getTitle();
        }
        if ($issue->getEpic()) {
            $data['fields']["customfield_10102"] = $issue->getEpic();
        }
        if (!empty($components)) {
            $data['fields']["components"] = $components;
        }

        return $data;
    }

    public function send($data)
    {
        $response = $this->client->request('POST', $this->url . '/rest/api/2/issue/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => $data
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201) {
            return $response->toArray();
        }
        return $response->getInfo();
    }

    public function updateStatus($key, $status, $statusJira)
    {
        $statusYandex = [
            'API' => 'Waiting',
            'Аналитика готова' => 'To Do',
            'Будем делать' => 'To Do',
            'В работе' => 'In Progress ',
            'Готово' => 'Ready for QA',
            'Демонстрация заказчику' => 'Ready for Demo',
            'Закрыт' => 'Resolved',
            'Открыт' => 'Backlog',
            'Отменено' => 'Resolved',
            'Аналитика' => 'Analytics ',
            'Решен' => 'Resolved',
            'Тестируется' => 'In QA',
        ];

        foreach ($statusJira['transitions'] as $statusJ) {
            if ($statusJ['name'] == $statusYandex[$status]) {
                $statusId = $statusJ['id'];
            }
        }
        $response = $this->client->request('POST', $this->url . '/rest/api/2/issue/' . $key . '/transitions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'transition' => [
                    'id' => $statusId
                ],
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 204) {
            return true;
        }

        return $response->getInfo();
    }

    public function getStatusJira($key)
    {
        $response = $this->client->request('GET', $this->url . '/rest/api/2/issue/' . $key . '/transitions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return $response->toArray();
        }

        return $response->getInfo();
    }

    public function addAttachments($key, $filePath)
    {
        $formFields = [
            'file' => DataPart::fromPath($filePath),
        ];
        $formData = new FormDataPart($formFields);
        $headersE = explode(':', $formData->getPreparedHeaders()->toArray()[0]);
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            "Accept" => "application/json",
            'X-Atlassian-Token' => 'no-check',
        ];
        $headers[$headersE[0]] = $headersE[1];
        $response = $this->client->request('POST', $this->url . '/rest/api/2/issue/' . $key . '/attachments', [
            'headers' => $headers,
            'body' => $formData->bodyToIterable()
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return true;
        }

        return $response->getInfo();
    }

    public function addComment(mixed $key, Comment $comment, $files = [])
    {
        $text = "Автор: " . $comment->getCreatedBy()['display'] . "\n\n" . $comment->getText();

        if (!empty($files)) {
            foreach ($files as $file) {
                $text .= "\n!" . basename($file) . "|thumbnail!";
            }
        }

        $response = $this->client->request('POST', $this->url . '/rest/api/2/issue/' . $key . '/comment', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => [
                'body' => $text
            ]
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 201) {
            return true;
        }

        return $response->getInfo();
    }
}