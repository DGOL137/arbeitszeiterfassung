<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ImpressumController extends AbstractController
{
    #[Route('/impressum', name: 'impressum')]
    public function index(ParameterBagInterface $parameters): Response
    {
        return $this->render('impressum/index.html.twig', [
        'impressum' => $parameters->get('impressum'),
    ]);
    }
}
