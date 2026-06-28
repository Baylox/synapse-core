<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\KnowledgeDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(KnowledgeDocumentRepository $documents): Response
    {
        return $this->render('chat/index.html.twig', [
            'documents' => $documents->findAllNewestFirst(),
        ]);
    }
}
