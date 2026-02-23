<?php

namespace App\Controller;

use App\Entity\Arbeitszeit;
use App\Entity\Mitarbeiter;
use App\Entity\Projekt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeineUebersichtController extends AbstractController
{
    #[Route('/meine_uebersicht', name: 'meine_uebersicht')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Mitarbeiter $mitarbeiter */
        $mitarbeiter = $this->getUser();

        // Sicherheitscheck (optional, aber sauber)
        if (!$mitarbeiter instanceof Mitarbeiter) {
            throw $this->createAccessDeniedException('Nicht autorisiert.');
        }

        // Filterwerte (Monat, Jahr, Projektnummer)
        $monat = (int) $request->query->get('monat', (int) date('n'));
        $jahr  = (int) $request->query->get('jahr', (int) date('Y'));
        $projektnummer = $request->query->get('projektnummer');

        // Zeitraum berechnen
        $von = new \DateTimeImmutable(sprintf('%04d-%02d-01', $jahr, $monat));
        $bis = $von->modify('last day of this month')->setTime(23, 59, 59);

        // 1️⃣ Eigene Arbeitszeiten im Zeitraum laden
        $qb = $em->getRepository(Arbeitszeit::class)->createQueryBuilder('a')
            ->join('a.projekt', 'p')
            ->andWhere('a.mitarbeiter = :mitarbeiter')
            ->andWhere('a.datum BETWEEN :von AND :bis')
            ->setParameter('mitarbeiter', $mitarbeiter)
            ->setParameter('von', $von)
            ->setParameter('bis', $bis);

        if ($projektnummer) {
            $qb->andWhere('p.projektnummer = :projektnummer')
               ->setParameter('projektnummer', $projektnummer);
        }

        $arbeitszeiten = $qb
            ->orderBy('a.datum', 'DESC')
            ->getQuery()
            ->getResult();

        // 2️⃣ Projekte fürs Dropdown:
        //     nur Projekte, zu denen der Mitarbeiter Arbeitszeiten hat
        $meineProjekte = $em->getRepository(Projekt::class)->createQueryBuilder('p')
            ->select('DISTINCT p')
            ->join('p.arbeitszeiten', 'a')
            ->where('a.mitarbeiter = :mitarbeiter')
            ->andWhere('a.datum BETWEEN :von AND :bis')
            ->setParameter('mitarbeiter', $mitarbeiter)
            ->setParameter('von', $von)
            ->setParameter('bis', $bis)
            ->orderBy('p.projektnummer', 'ASC')
            ->getQuery()
            ->getResult();

        $aktuellesJahr = (int) date('Y');

        return $this->render('meine_uebersicht/index.html.twig', [
            'arbeitszeiten'   => $arbeitszeiten,
            'meineProjekte'   => $meineProjekte,
            'monat'           => $monat,
            'jahr'            => $jahr,
            'projektnummer'   => $projektnummer,
            'aktuellesJahr'   => $aktuellesJahr,
        ]);
    }
}
