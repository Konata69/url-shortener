<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocController extends AbstractController
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @Route("/api/docs", name="api-docs", methods={"GET"})
     */
    public function docs(): Response
    {
        $path = $this->projectDir . '/templates/docs.html';
        $content = file_get_contents($path);
        return new Response($content);
    }
}
