<?php

namespace App\Controller;

use App\Entity\Mitarbeiter;
use App\Repository\DatenschutzStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeineDatenController extends AbstractController
{
    #[Route('/meine_daten_anzeigen', name: 'meine_daten_anzeigen', methods: ['GET'])]
    public function anzeigen(): Response
    {
        return $this->render('meine_daten/index.html.twig');
    }

    #[Route('/meine_daten_speichern', name: 'meine_daten_speichern', methods: ['POST'])]
    public function speichern(
        Request $request,
        EntityManagerInterface $em,
        DatenschutzStatusRepository $dsRepository
    ): Response {
        $token = $request->request->get('_csrf_token');

if (!$this->isCsrfTokenValid('meine_daten', $token)) {
    throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
}

        /** @var Mitarbeiter $mitarbeiter */
        $mitarbeiter = $this->getUser();

        if (!$mitarbeiter instanceof Mitarbeiter) {
            throw $this->createAccessDeniedException('Nicht autorisiert.');
        }

        // Eingaben trimmen
        $vorname = trim($request->request->get('vorname'));
        $nachname = trim($request->request->get('nachname'));
        $email = trim($request->request->get('email'));
        $dsBezeichnung = $request->request->get('datenschutzStatus');

        // Leere Felder prüfen
        if (empty($vorname) || empty($nachname) || empty($email)) {
            $this->addFlash('error', 'Bitte füllen Sie alle Felder aus.');
            return $this->redirectToRoute('meine_daten_anzeigen');
        }

        // Vorname/Nachname: nur Buchstaben (inkl. Umlaute), Leerzeichen, Bindestrich
        if (!preg_match('/^[\p{L}\s\-]+$/u', $vorname) || !preg_match('/^[\p{L}\s\-]+$/u', $nachname)) {
            $this->addFlash('error', 'Vor- und Nachname dürfen nur Buchstaben, Leerzeichen und Bindestriche enthalten.');
            return $this->redirectToRoute('meine_daten_anzeigen');
        }

        // E-Mail: einfache Strukturprüfung
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Bitte geben Sie eine gültige E-Mail-Adresse ein.');
            return $this->redirectToRoute('meine_daten_anzeigen');
        }

        // Datenschutzstatus ändern
        if ($dsBezeichnung) {
            $status = $dsRepository->findOneBy(['bezeichnung' => $dsBezeichnung]);
            if ($status) {
                $mitarbeiter->setDatenschutzStatus($status);
            } else {
                $this->addFlash('error', 'Der ausgewählte Datenschutzstatus ist ungültig.');
                return $this->redirectToRoute('meine_daten_anzeigen');
            }
        }

        // Daten speichern
        $mitarbeiter->setVorname($vorname);
        $mitarbeiter->setNachname($nachname);
        $mitarbeiter->setEmail($email);
        $mitarbeiter->setLetzteaenderung(new \DateTime());

        $em->persist($mitarbeiter);
        $em->flush();

        $this->addFlash('success', 'Ihre Daten wurden erfolgreich gespeichert.');

        return $this->redirectToRoute('meine_daten_anzeigen');
    }
}
