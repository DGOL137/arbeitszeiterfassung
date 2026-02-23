<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HilfeController extends AbstractController
{
    #[Route('/hilfe', name: 'hilfe')]
    public function index(): Response
    {
        return $this->render('hilfe/index.html.twig', [
            'controller_name' => 'HilfeController',
        ]);
    }
}
