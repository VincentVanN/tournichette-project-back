<?php

namespace App\Utils;

use App\Entity\User;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
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
    private $baseUrl;
    private $router;

    public function __construct(
        MailerInterface $mailer,
        Int $mailExpiredTime,
        string $mailFrom,
        GetBaseUrl $baseUrl,
        UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->mailExpiredTime = $mailExpiredTime;
        $this->mailFrom = new Address($mailFrom, 'Webmaster de La Tournichette');
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
            // ->to($user->getEmail())
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