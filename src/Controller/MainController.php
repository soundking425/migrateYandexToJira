<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\YandexTracker\Issues;

class MainController extends AbstractController
{
    private Issues $issues;

    public function __construct(Issues $issues)
    {
        $this->issues = $issues;
    }

    #[Route('/', name: 'app_homepage')]
    public function homePage()
    {
        return $this->json();
    }

    #[Route('/issues/{queue}/{page}', name: 'load_issues')]
    public function loadIssues($queue, $page)
    {
        $res = $this->issues->loadIssues($queue, $page);
        return $this->json($res);
    }
}