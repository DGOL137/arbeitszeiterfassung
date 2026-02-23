<?php

namespace App\Controller;

use App\Repository\ArbeitszeitRepository;
use App\Repository\ProjektRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_PROJEKTSACHBEARBEITUNG')]
class ProjektverwaltungController extends AbstractController
{
    #[Route('/projektverwaltung', name: 'projektverwaltung')]
    public function index(
        Request $request,
        ArbeitszeitRepository $arbeitszeitRepository,
        ProjektRepository $projektRepository
    ): Response {
        $user = $this->getUser();
        $monat = (int) $request->query->get('monat', date('n'));
        $jahr = (int) $request->query->get('jahr', date('Y'));
        $projektnummer = $request->query->get('projektnummer');

        // Berechne Zeitraum
        $start = new \DateTimeImmutable(sprintf('%04d-%02d-01', $jahr, $monat));
        $ende = $start->modify('last day of this month')->setTime(23, 59, 59);

        // Starte QueryBuilder
        $qb = $arbeitszeitRepository->createQueryBuilder('a')
            ->leftJoin('a.projekt', 'p')
            ->leftJoin('a.mitarbeiter', 'm')
            ->addSelect('p', 'm')
            ->where('a.datum BETWEEN :start AND :ende')
            ->setParameter('start', $start)
            ->setParameter('ende', $ende);

        // Projektnummer-Filter (optional)
        if ($projektnummer) {
            $qb->andWhere('p.projektnummer = :projektnummer')
               ->setParameter('projektnummer', $projektnummer);
        }

        $arbeitszeiten = $qb->orderBy('a.datum', 'ASC')->getQuery()->getResult();

        return $this->render('projektverwaltung/index.html.twig', [
            'arbeitszeiten' => $arbeitszeiten,
            'monat' => $monat,
            'jahr' => $jahr,
            'projektnummer' => $projektnummer,
            'alleProjekte' => $projektRepository->findAll(),
            'aktuellesJahr' => (int) date('Y'),
        ]);
    }
}
