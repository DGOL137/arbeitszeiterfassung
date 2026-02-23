<?php

namespace App\Controller;

use App\Repository\ArbeitszeitRepository;
use App\Repository\MitarbeiterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PERSONALSACHBEARBEITUNG')]
class PersonalverwaltungController extends AbstractController
{
    #[Route('/personalverwaltung', name: 'personalverwaltung')]
    public function index(
        Request $request,
        ArbeitszeitRepository $arbeitszeitRepository,
        MitarbeiterRepository $mitarbeiterRepository
    ): Response {
        $monat = (int) $request->query->get('monat', date('n'));
        $jahr = (int) $request->query->get('jahr', date('Y'));
        $mitarbeiternummer = $request->query->get('mitarbeiternummer');

        // Zeitraum berechnen
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $jahr, $monat));
        $ende = $start->modify('last day of this month')->setTime(23, 59, 59);

        // QueryBuilder aufbauen
        $qb = $arbeitszeitRepository->createQueryBuilder('a')
            ->leftJoin('a.mitarbeiter', 'm')
            ->addSelect('m')
            ->where('a.datum BETWEEN :start AND :ende')
            ->setParameter('start', $start)
            ->setParameter('ende', $ende);

        // Optional nach Mitarbeitenden filtern
        if ($mitarbeiternummer) {
            $qb->andWhere('m.mitarbeiternummer = :mitarbeiternummer')
               ->setParameter('mitarbeiternummer', $mitarbeiternummer);
        }

        $arbeitszeiten = $qb->orderBy('a.datum', 'ASC')->getQuery()->getResult();

        return $this->render('personalverwaltung/index.html.twig', [
            'arbeitszeiten' => $arbeitszeiten,
            'monat' => $monat,
            'jahr' => $jahr,
            'mitarbeiternummer' => $mitarbeiternummer,
            'alleMitarbeiter' => $mitarbeiterRepository->findAll(),
            'aktuellesJahr' => (int) date('Y'),
        ]);
    }
}
