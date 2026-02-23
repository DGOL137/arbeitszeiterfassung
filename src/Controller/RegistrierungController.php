<?php

namespace App\Controller;

use App\Entity\Mitarbeiter;
use App\Repository\RolleRepository;
use App\Repository\DatenschutzStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegistrierungController extends AbstractController
{
    #[Route('/registrierung', name: 'registrierung')]
    public function index(): Response
    {
        return $this->render('registrierung/index.html.twig');
    }

    #[Route('/registrierung_durchfuehren', name: 'registrierung_durchfuehren', methods: ['GET', 'POST'])]
    public function registrieren(
        Request $request,
        ValidatorInterface $validator,
        RolleRepository $rolleRepository,
        DatenschutzStatusRepository $dsRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $csrfToken = $request->request->get('_csrf_token');

    if (!$this->isCsrfTokenValid('registrierung', $csrfToken)) {
        throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
    }
            $passwort = $request->request->get('inputPassword');
            $passwortWiederholt = $request->request->get('inputPasswordRpt');

            // ✅ Datenschutz-Haken prüfen
            if (!$request->request->get('inputPrivacy')) {
                $this->addFlash('danger', 'Du musst der Datenschutzerklärung zustimmen.');
                return $this->redirectToRoute('registrierung');
            }

            if ($passwort !== $passwortWiederholt) {
                $this->addFlash('danger', 'Die Passwörter stimmen nicht überein.');
                return $this->redirectToRoute('registrierung');
            }

            $mitarbeiter = new Mitarbeiter();
            $mitarbeiter->setVorname($request->request->get('inputFirstname'));
            $mitarbeiter->setNachname($request->request->get('inputLastname'));
            $mitarbeiter->setEmail($request->request->get('inputEmail'));
            $mitarbeiter->setMitarbeiternummer((int) $request->request->get('inputEmployeenumber'));
            $mitarbeiter->setPassworthash(password_hash($passwort, PASSWORD_DEFAULT));
            $mitarbeiter->setRegistrierungsdatum(new \DateTime());

            // Standard-Rolle setzen
            $rolle = $rolleRepository->findOneBy(['bezeichnung' => 'Mitarbeiter']);
            if ($rolle) {
                $mitarbeiter->setRolle($rolle);
            }

            // Standard-Datenschutzstatus setzen
            $dsStatus = $dsRepository->findOneBy(['bezeichnung' => 'zugestimmt']);
            if ($dsStatus) {
                $mitarbeiter->setDatenschutzStatus($dsStatus);
            }

            // Validierung
            $errors = $validator->validate($mitarbeiter);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error->getMessage());
                }
                return $this->redirectToRoute('registrierung');
            }

            // Speichern in DB
            $entityManager->persist($mitarbeiter);
            $entityManager->flush();

            $this->addFlash('success', 'Registrierung erfolgreich gespeichert!');

            return $this->redirectToRoute('anmeldung');
        }

        return $this->render('registrierung/index.html.twig');
    }
}
