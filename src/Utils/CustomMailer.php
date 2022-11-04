<?php

namespace App\Utils;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This service sends Emails
 */
class CustomMailer
{
    private $mailer;
    private $mailExpiredTime;
    private $mailFrom;
    private $mailAdmin;
    private $baseUrl;
    private $router;

    public function __construct(
        MailerInterface $mailer,
        Int $mailExpiredTime,
        string $mailFrom,
        string $mailAdmin,
        GetBaseUrl $baseUrl,
        UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->mailExpiredTime = $mailExpiredTime;
        $this->mailFrom = new Address($mailFrom, 'Webmaster de La Tournichette');
        $this->mailAdmin = new Address($mailAdmin, 'Le Panier de la Tournichette');
        $this->baseUrl = $baseUrl;
        $this->router = $router;

    }

    /**
     * Send an email to the user in parameter, with a link to confirm it
     * 
     * @param User $user The user for mail verification
     */
    public function emailVerify(User $user)
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to($user->getEmail())
            ->subject('Vérification de votre adresse email')
            ->htmlTemplate('mailer/email_verify/verify.html.twig')
            ->context([
                'user' => $user,
                'validity' => $this->mailExpiredTime,
                'urlChecker' => $this->baseUrl->getMailerUrl() . $this->router->generate('app_mailer_email_verify', [
                    'email' => $user->getEmail(),
                    'token' => $user->getEmailToken()
                ])
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // TODO gérer les erreurs
        } finally {
            return;
        }
    }

    public function emailResetPassword(User $user)
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to($user->getEmail())
            ->subject('Demande de réinitialisation de votre mot de passe')
            ->htmlTemplate('mailer/reset_password/reset.html.twig')
            ->context([
                'user' => $user,
                'validity' => $this->mailExpiredTime,
                'urlChecker' => $this->baseUrl->getMailerUrl() . $this->router->generate('app_mailer_password_reset', [
                    'email' => $user->getEmail(),
                    'token' => $user->getTempToken()
                ])
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // TODO gérer les erreurs
        } finally {
            return;
        }
    }

    public function emailConfirmResetPassword(User $user)
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to($user->getEmail())
            ->subject('Modification de votre mot de passe')
            ->htmlTemplate('mailer/reset_password/confirm.html.twig')
            ->context(['user' => $user]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // TODO gérer les erreurs
        } finally {
            return;
        }
    }

    public function sendSalesNotification($users, string $subject, string $message)
    {
        $email = (new TemplatedEmail())
            ->from($this->mailAdmin)
            ->subject($subject)
            ->htmlTemplate('mailer/email_status_sales/show.html.twig')
            ->context(['message' => $message]);
            
        foreach ($users as $currentUser) {
            $email->to($currentUser->getEmail());
            $this->mailer->send($email);
        }
        
        return;
    }


    /**
     * Get the value of mailFrom
     */ 
    public function getMailFrom()
    {
        return $this->mailFrom;
    }

    /**
     * Get the value of mailExpiredTime
     */ 
    public function getMailExpiredTime(): int
    {
        return $this->mailExpiredTime;
    }
}