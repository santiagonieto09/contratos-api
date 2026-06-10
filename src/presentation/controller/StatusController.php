<?php

namespace App\presentation\controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class StatusController extends AbstractController
{
    #[Route('/', name: 'api_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'aplicacion' => 'Contratos API',
            'version' => '1.0.0',
            'estado' => 'ok'
        ]);
    }
}
