<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class AbmeldungController extends AbstractController
{
    #[Route('/abmeldung', name: 'abmeldung')]
    public function logout(): void
    {
        // Dieser Code wird nie ausgeführt!
        // Symfony Security fängt den Request vorher ab.
        throw new \LogicException('This should never be reached.');
    }
}
