<?php

namespace App\Controller;

use App\Entity\Arbeitszeit;
use App\Entity\Mitarbeiter;
use App\Entity\Projekt;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErfassungController extends AbstractController
{
    #[Route('/erfassung', name: 'erfassung', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var Mitarbeiter $mitarbeiter */
        $mitarbeiter = $this->getUser();

        $heute = new DateTimeImmutable('today');

        $laufend = $em->getRepository(Arbeitszeit::class)->findOneBy([
            'mitarbeiter' => $mitarbeiter,
            'endzeit' => null,
            'datum' => $heute,
        ]);

        $projekte = $em->getRepository(Projekt::class)->findAll();

        return $this->render('erfassung/index.html.twig', [
            'aktiveErfassung' => $laufend,
            'projekte' => $projekte,
            'heute' => $heute,
        ]);
    }

    #[Route('/erfassung/start', name: 'erfassung_start', methods: ['POST'])]
    public function start(Request $request, EntityManagerInterface $em): Response
    {

    $token = $request->request->get('_csrf_token');

if (!$this->isCsrfTokenValid('erfassung_start', $token)) {
    throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
}

        /** @var Mitarbeiter $mitarbeiter */
        $mitarbeiter = $this->getUser();

        if (!$mitarbeiter) {
            throw $this->createAccessDeniedException();
        }

        $projektnummer = trim($request->request->get('projektnummer'));
        $bemerkung = trim($request->request->get('bemerkung'));

        if (!$projektnummer || !preg_match('/^\d{5}$/', $projektnummer)) {
            $this->addFlash('error', 'Bitte geben Sie eine gültige 5-stellige Projektnummer ein.');
            return $this->redirectToRoute('erfassung');
        }

        // Projekt laden oder neu anlegen
        $projekt = $em->getRepository(Projekt::class)->findOneBy(['projektnummer' => $projektnummer]);
        if (!$projekt) {
            $projekt = new Projekt();
            $projekt->setProjektnummer($projektnummer);
            $em->persist($projekt);
        }

        // Prüfen, ob schon eine Erfassung läuft
        $heute = new \DateTimeImmutable('today');
        $bereitsGestartet = $em->getRepository(Arbeitszeit::class)->findOneBy([
            'mitarbeiter' => $mitarbeiter,
            'datum' => $heute,
            'endzeit' => null,
        ]);

        if ($bereitsGestartet) {
            $this->addFlash('error', 'Sie haben bereits eine laufende Erfassung.');
            return $this->redirectToRoute('erfassung');
        }

        // Neue Erfassung starten
        $arbeitszeit = new Arbeitszeit();
        $arbeitszeit->setMitarbeiter($mitarbeiter);
        $arbeitszeit->setProjekt($projekt);
        $arbeitszeit->setDatum($heute);
        $arbeitszeit->setStartzeit(new \DateTimeImmutable());
        $arbeitszeit->setBemerkung($bemerkung);

        $em->persist($arbeitszeit);
        $em->flush();

        $this->addFlash('success', 'Zeiterfassung gestartet.');
        return $this->redirectToRoute('erfassung');
    }

    #[Route('/erfassung/ende', name: 'erfassung_ende', methods: ['POST'])]
    public function ende(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->request->get('_csrf_token');

if (!$this->isCsrfTokenValid('erfassung_ende', $token)) {
    throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
}

        /** @var Mitarbeiter $mitarbeiter */
        $mitarbeiter = $this->getUser();

        $laufend = $em->getRepository(Arbeitszeit::class)->findOneBy([
            'mitarbeiter' => $mitarbeiter,
            'datum' => new \DateTimeImmutable('today'),
            'endzeit' => null,
        ]);

        if (!$laufend) {
            $this->addFlash('error', 'Es läuft keine aktive Zeiterfassung.');
            return $this->redirectToRoute('erfassung');
        }

        $laufend->setEndzeit(new \DateTimeImmutable());
        $em->flush();

        $this->addFlash('success', 'Zeiterfassung beendet.');
        return $this->redirectToRoute('erfassung');
    }
}
