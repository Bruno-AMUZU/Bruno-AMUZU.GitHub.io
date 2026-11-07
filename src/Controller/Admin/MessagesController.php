<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\HttpException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Model\Repository\MessageRepository;

/**
 * Messages reçus (design/pages/admin-messages.md) : liste filtrable et détail.
 * Desktop : deux panneaux. Mobile : la liste seule ; un message s'ouvre en plein écran avec un lien retour.
 */
final class MessagesController extends AdminController
{
    private const FILTERS = ['non-lus' => 'unread', 'tous' => 'all', 'archives' => 'archived'];

    public function __construct(
        private readonly Request $request,
        private readonly Session $session,
        private readonly MessageRepository $messages,
    ) {
    }

    /** GET /admin/messages */
    public function index(): Response
    {
        return $this->page(null);
    }

    /** GET /admin/messages/{id} : ouvrir un message le marque comme lu. */
    public function show(string $id): Response
    {
        $message = $this->messages->find((int) $id) ?? throw new HttpException(404);
        if ($message->isUnread()) {
            $this->messages->setStatus($message->id, 'read');
            $message = $this->messages->find($message->id);
        }

        return $this->page($message);
    }

    /** POST /admin/messages/{id}/archiver (ou désarchiver si déjà archivé) */
    public function archive(string $id): Response
    {
        $message = $this->messages->find((int) $id) ?? throw new HttpException(404);
        $this->messages->setStatus($message->id, $message->status === 'archived' ? 'read' : 'archived');
        $this->session->flash('notice', t($message->status === 'archived' ? 'admin.messages.unarchived' : 'admin.messages.archived'));

        return $this->redirect('/admin/messages');
    }

    /** POST /admin/messages/{id}/non-lu */
    public function markUnread(string $id): Response
    {
        $this->messages->setStatus((int) $id, 'unread');

        return $this->redirect('/admin/messages');
    }

    /** POST /admin/messages/{id}/supprimer */
    public function destroy(string $id): Response
    {
        $this->messages->delete((int) $id);
        $this->session->flash('notice', t('admin.messages.deleted'));

        return $this->redirect('/admin/messages');
    }

    private function page(?\App\Model\Entity\Message $selected): Response
    {
        $slug = $this->request->query('filtre') ?? 'tous';
        $filter = self::FILTERS[$slug] ?? 'all';

        return $this->render('admin/messages', [
            'title' => t('admin.messages.title'),
            'messages' => $this->messages->list($filter),
            'selected' => $selected,
            'filter' => $slug,
            'filters' => self::FILTERS,
        ]);
    }
}
