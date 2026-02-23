<?php

namespace App\Controller;

use App\Entity\Mitarbeiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private string $mailFromAddress,
        private string $mailFromName
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
#[Route('/passwort-vergessen', name: 'passwort-vergessen', methods: ['GET'])]
public function request(): Response
{
    return $this->render('reset_password/request.html.twig');
}

#[Route('/passwort-anfordern', name: 'passwort-anfordern', methods: ['POST'])]
public function checkEmail(Request $request, MailerInterface $mailer): Response
{
    $csrfToken = $request->request->get('_csrf_token');

if (!$this->isCsrfTokenValid('passwort_anfordern', $csrfToken)) {
    throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
}

       $emailadresse = $request->request->get('inputEmail', '');

    if ($request->isMethod('POST') && ($request->request->get('inputEmail') !== null)  && ($request->request->get('inputEmail') !== '') && filter_var($emailadresse, FILTER_VALIDATE_EMAIL)) {

        $this->processSendingPasswordResetEmail($emailadresse, $mailer);

                $this->addFlash(
            'success',
            'Wir haben Ihnen einen Link zum Zurücksetzen gesendet, falls ein Konto mit dieser E-Mail existiert.'
        );

        return $this->render('reset_password/check_email.html.twig');
    }
    else {
        $this->addFlash(
            'danger',
            'Bitte geben Sie eine gültige E-Mail-Adresse ein.'
        );
        return $this->render('reset_password/check_email.html.twig');
    }
}


    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
#[Route('/passwort-zuruecksetzen/{token}', name: 'passwort-zuruecksetzen-token', methods: ['GET'])]
        public function storeResetToken(string $token): Response
{
    if (empty($token)) {
        $this->addFlash('danger', 'Der Zurücksetzen-Link ist ungültig.');
        return $this->redirectToRoute('passwort-vergessen');
    }

    $this->storeTokenInSession($token);

    return $this->redirectToRoute('passwort-zuruecksetzen');
}

#[Route('/passwort-zuruecksetzen', name: 'passwort-zuruecksetzen', methods: ['GET', 'POST'])]
public function reset(
    Request $request,
    UserPasswordHasherInterface $passwordHasher
): Response {
    $token = $this->getTokenFromSession();

    if (!$token) {
        $this->addFlash('danger', 'Der Zurücksetzen-Link ist ungültig oder abgelaufen.');
        return $this->redirectToRoute('passwort-vergessen');
    }

    try {
        /** @var Mitarbeiter $mitarbeiter */
        $mitarbeiter = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
    } catch (ResetPasswordExceptionInterface $e) {
        $this->addFlash('danger', 'Der Zurücksetzen-Link ist ungültig oder abgelaufen.');
        return $this->redirectToRoute('passwort-vergessen');
    }

    // 👉 NUR bei POST Passwort setzen
    if ($request->isMethod('POST')) {
            $csrfToken = $request->request->get('_csrf_token');

if (!$this->isCsrfTokenValid('passwort_zuruecksetzen', $csrfToken)) {
    throw $this->createAccessDeniedException('Ungültiger CSRF-Token');
}
        $passwort = (string) $request->request->get('inputPassword');
        $passwortRpt = (string) $request->request->get('inputPasswordRpt');

        if ($passwort === '' || $passwortRpt === '') {
            $this->addFlash('danger', 'Bitte füllen Sie beide Passwortfelder aus.');
        } elseif ($passwort !== $passwortRpt) {
            $this->addFlash('danger', 'Die Passwörter stimmen nicht überein.');
        } else {
            $mitarbeiter->setPassword(
                $passwordHasher->hashPassword($mitarbeiter, $passwort)
            );

            $this->entityManager->flush();
            $this->resetPasswordHelper->removeResetRequest($token);
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Das Passwort wurde erfolgreich zurückgesetzt.');
            return $this->redirectToRoute('anmeldung');
        }
    }

    // GET oder POST mit Fehler → Formular anzeigen
    return $this->render('reset_password/reset.html.twig');
}


    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): void
    {
        $mitarbeiter = $this->entityManager->getRepository(Mitarbeiter::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        if (!$mitarbeiter) {
            return;
        }

        try {

        $vorname = $mitarbeiter->getVorname();
        $nachname = $mitarbeiter->getNachname();
        $emailAdresse = $mitarbeiter->getEmail();
        $token = $this->resetPasswordHelper->generateResetToken($mitarbeiter);
        $link = $this->generateUrl('passwort-zuruecksetzen-token', ['token' => $token->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $tokenDauer = $this->resetPasswordHelper->getTokenLifetime() / 60;

        } catch (ResetPasswordExceptionInterface $e) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('mail_from_address'), $this->getParameter('mail_from_name')))
            ->to($emailAdresse)
            ->subject('Passwort zurücksetzen')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'vorname' => $vorname,
                'nachname' => $nachname,
                'link' => $link,
                'tokenDauer' => $tokenDauer,
            ]);

        $mailer->send($email);
        $this->setTokenObjectInSession($token);

        return;
    }
}
