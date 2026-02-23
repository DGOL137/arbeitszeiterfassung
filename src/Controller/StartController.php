<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StartController extends AbstractController
{
    #[Route('/start', name: 'start')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $vorname = $session->get('vorname');
        $nachname = $session->get('nachname');

        return $this->render('start/index.html.twig', [
            'vorname' => $vorname,
            'nachname' => $nachname,
        ]);
    }
}
