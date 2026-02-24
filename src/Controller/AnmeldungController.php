<?php

namespace App\Controller;

use App\Entity\Mitarbeiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnmeldungController extends AbstractController
{
    #[Route('/anmeldung', name: 'anmeldung', methods: ['GET', 'POST'])]
    public function login(Request $request, EntityManagerInterface $em): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('start');
        }
        else {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('inputEmail');
            $passwort = $request->request->get('inputPassword');

            // Suche nach Mitarbeiter mit passender E-Mail
            $mitarbeiter = $em->getRepository(Mitarbeiter::class)
                ->findOneBy(['email' => $email]);

            if (!$mitarbeiter) {
                $this->addFlash('danger', 'Anmeldung fehlgeschlagen. Bitte überprüfen Sie Ihre E-Mail-Adresse oder Ihr Passwort.');
                return $this->redirectToRoute('anmeldung');
            }

            // Passwort prüfen
            if (!password_verify($passwort, $mitarbeiter->getPassworthash())) {
                $this->addFlash('danger', 'Anmeldung fehlgeschlagen. Bitte überprüfen Sie Ihre E-Mail-Adresse oder Ihr Passwort.');
                return $this->redirectToRoute('anmeldung');
            }

            // Erfolgreich: z. B. Nutzer-ID in Session speichern (temporäre Lösung)
            $session = $request->getSession();
            $session->set('benutzer_id', $mitarbeiter->getMitarbeiterId());
            $session->set('vorname', $mitarbeiter->getVorname());
            $session->set('nachname', $mitarbeiter->getNachname());


            $this->addFlash('success', 'Anmeldung erfolgreich!');
            return $this->redirectToRoute('start');
        }
        }

        return $this->render('anmeldung/index.html.twig');
    }
}
