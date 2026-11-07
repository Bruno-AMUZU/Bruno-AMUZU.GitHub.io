<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Throttle;
use App\Core\View;
use App\Model\Entity\Message;
use App\Model\Repository\MessageRepository;

/**
 * Contact (design/pages/contact.md) : formulaire, puis écran « Message envoyé ».
 *
 * Le message est d'abord enregistré en base (visible dans le back-office) ; l'e-mail n'est qu'une
 * notification : s'il échoue, rien n'est perdu.
 */
final class ContactController
{
    public function __construct(
        private readonly Request $request,
        private readonly Session $session,
        private readonly Throttle $throttle,
        private readonly MessageRepository $messages,
        private readonly Mailer $mailer,
    ) {
    }

    /** GET /fr/contact, /en/contact */
    public function show(string $lang): string
    {
        $errors = [];
        foreach ((array) $this->session->getFlash('errors', []) as $field => $key) {
            $errors[$field] = t('form.errors.' . $key);
        }

        return View::render('site/contact', [
            'title' => t('common.nav.contact'),
            'description' => t('contactPage.title'),
            'sent' => (bool) $this->session->getFlash('contact_sent', false),
            'errors' => $errors,
            'old' => (array) $this->session->getFlash('old', []),
        ]);
    }

    /** POST /fr/contact, /en/contact */
    public function send(string $lang): Response
    {
        $back = Response::redirect(url('contact') . '#contact-title');

        // Champ piège rempli = robot : on fait comme si tout s'était bien passé, sans rien enregistrer.
        if ((string) $this->request->input('website') !== '') {
            $this->session->flash('contact_sent', true);

            return $back;
        }

        $input = [
            'name' => mb_substr((string) $this->request->input('name'), 0, 200),
            'email' => mb_substr((string) $this->request->input('email'), 0, 200),
            'type' => (string) $this->request->input('type'),
            'message' => mb_substr((string) $this->request->input('message'), 0, 6000),
        ];

        $errors = [];
        if ($input['name'] === '') {
            $errors['name'] = 'required';
        } elseif (mb_strlen($input['name']) > 120) {
            $errors['name'] = 'tooLong';
        }
        if ($input['email'] === '') {
            $errors['email'] = 'required';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'email';
        }
        if ($input['message'] === '') {
            $errors['message'] = 'required';
        } elseif (mb_strlen($input['message']) > 5000) {
            $errors['message'] = 'tooLong';
        }

        // 5 messages par heure et par adresse IP.
        $key = 'contact:' . $this->request->ip();
        if ($errors === [] && $this->throttle->tooMany($key, 5, 3600)) {
            $errors['message'] = 'throttled';
        }

        if ($errors !== []) {
            $this->session->flashForm($errors, $input);

            return $back;
        }

        $this->throttle->hit($key);
        $type = Message::TYPES[(int) $input['type']] ?? 'other';
        $this->messages->create($input['name'], $input['email'], $type, $input['message'], $lang);
        $this->notify($input, $type);

        $this->session->flash('contact_sent', true);

        return $back;
    }

    /** @param array<string, string> $input */
    private function notify(array $input, string $type): void
    {
        $typeLabel = t_list('form.types')[array_search($type, Message::TYPES, true)] ?? $type;
        $this->mailer->send(
            setting('contact_email', 'amuzuelbruno@gmail.com'),
            'Nouveau message de ' . $input['name'] . ' (' . $typeLabel . ')',
            "Nom : {$input['name']}\nEmail : {$input['email']}\nType : {$typeLabel}\n\n{$input['message']}\n\n"
                . '— Message enregistré dans le back-office : ' . rtrim((string) \App\Core\Env::get('APP_URL', ''), '/') . '/admin/messages',
            $input['email'],
            $input['name'],
        );
    }
}
