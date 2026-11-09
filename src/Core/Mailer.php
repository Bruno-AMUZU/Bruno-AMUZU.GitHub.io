<?php

declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Envoi d'e-mails par SMTP (PHPMailer), configuré par .env :
 * Mailpit en développement (rien ne part vraiment), vrai serveur SMTP en production.
 */
final class Mailer
{
    /**
     * @param string|null $replyTo adresse de réponse (ex. le visiteur qui a écrit via le formulaire)
     * @return bool true si le serveur SMTP a accepté le message
     */
    public function send(string $to, string $subject, string $text, ?string $replyTo = null, ?string $replyName = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = Env::get('MAIL_HOST', 'localhost') ?? 'localhost';
            $mail->Port = (int) Env::get('MAIL_PORT', '587');
            $user = Env::get('MAIL_USER', '') ?? '';
            if ($user !== '') {
                $mail->SMTPAuth = true;
                $mail->Username = $user;
                $mail->Password = Env::get('MAIL_PASSWORD', '') ?? '';
                $mail->SMTPSecure = $mail->Port === 465 ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPAutoTLS = false; // Mailpit, sans authentification ni TLS
            }

            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->setFrom(Env::get('MAIL_FROM', 'no-reply@brunoamuzu.fr') ?? '', Env::get('MAIL_FROM_NAME', 'Portfolio') ?? '');
            $mail->addAddress($to);
            if ($replyTo !== null) {
                $mail->addReplyTo($replyTo, $replyName ?? '');
            }
            $mail->Subject = $subject;
            $mail->Body = $text; // texte brut : aucun HTML venant d'un visiteur dans les e-mails
            $mail->isHTML(false);

            return $mail->send();
        } catch (MailException $e) {
            App::get(Logger::class)->error('Envoi d\'e-mail impossible : ' . $mail->ErrorInfo, ['to' => $to]);

            return false;
        }
    }
}
