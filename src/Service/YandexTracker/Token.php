<?php

namespace App\Service\YandexTracker;

use App\Repository\TokenRepository;
use App\Service\TokenInterdace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Token implements TokenInterdace
{
    private $token;
    /**
     * @var HttpClientInterface
     */
    private $client;
    private $tokenYandex;
    private TokenRepository $tokenRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        $tokenYandex,
        HttpClientInterface $client,
        TokenRepository $tokenRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->client = $client;
        $this->tokenYandex = $tokenYandex;
        $this->tokenRepository = $tokenRepository;
        $this->entityManager = $entityManager;
    }

    public function getToken()
    {
        $token = $this->tokenRepository->findToken('yandex');
        if ($token) {
            return $token[0]->getToken();
        }

        $this->requestToken();
        return $this->token;
    }

    public function setToken($token)
    {
        $newToken = new \App\Entity\Token();
        $newToken
            ->setToken($token)
            ->setType('yandex')
            ->setCreateAt(new \DateTimeImmutable())
        ;
        $this->entityManager->persist($newToken);
        $this->entityManager->flush();

        $this->token = $token;
    }

    public function requestToken()
    {
        $response = $this->client->request('POST', 'https://iam.api.cloud.yandex.net/iam/v1/tokens', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => json_encode([
                'yandexPassportOauthToken' => $this->tokenYandex
            ])
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            $content = $response->toArray();
            $this->setToken($content['iamToken']);
            return $content;
        }

        return false;
    }

}