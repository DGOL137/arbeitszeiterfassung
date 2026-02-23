<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DatenschutzController extends AbstractController
{
    #[Route('/datenschutz', name: 'datenschutz')]
    public function index(): Response
    {
        return $this->render('datenschutz/index.html.twig', [
            'controller_name' => 'DatenschutzController',
        ]);
    }
}
