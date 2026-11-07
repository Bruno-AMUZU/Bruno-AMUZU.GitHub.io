<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Resource\Field;
use App\Controller\Admin\Resource\ResourceForm;
use App\Core\Response;
use App\Core\Session;
use App\Core\UploadException;
use App\Model\Repository\SettingRepository;
use App\Model\Repository\TranslatableRepository;

/**
 * Réglages du site : disponibilité, e-mail de contact, liens réseaux, CV (PDF), photo ; textes du hero FR/EN.
 * Un seul formulaire, enregistré dans la table settings.
 */
final class SettingsController extends AdminController
{
    public function __construct(
        private readonly Session $session,
        private readonly ResourceForm $form,
        private readonly SettingRepository $settings,
    ) {
    }

    /** @return list<Field> */
    private function fields(): array
    {
        $f = 'admin.settings.fields.';

        return [
            Field::text('hero_status', t($f . 'heroStatus'))->translated()->max(80)->placeholder(t('home.hero.status')),
            Field::textarea('hero_title', t($f . 'heroTitle'))->translated()->max(140)->placeholder(t('home.hero.title')),
            Field::text('hero_subtitle', t($f . 'heroSubtitle'))->translated()->max(140)->placeholder(t('home.hero.subtitle')),

            Field::toggle('available', t($f . 'available'))->help(t($f . 'availableHelp'))->in(t('admin.settings.panels.availability')),
            Field::email('contact_email', t($f . 'email'))->required()->max(190)->in(t('admin.settings.panels.contact')),
            Field::url('github_url', 'GitHub')->max(300)->in(t('admin.settings.panels.contact')),
            Field::url('linkedin_url', 'LinkedIn')->max(300)->in(t('admin.settings.panels.contact')),
            Field::url('x_url', 'X')->max(300)->in(t('admin.settings.panels.contact')),
            Field::pdf('cv_file_id', t($f . 'cv'))->in(t('admin.settings.panels.files')),
            Field::image('photo_file_id', t($f . 'photo'))->help(t($f . 'photoHelp'))->in(t('admin.settings.panels.files')),
        ];
    }

    /** GET /admin/reglages */
    public function edit(): Response
    {
        return $this->page($this->values(), []);
    }

    /** POST /admin/reglages */
    public function update(): Response
    {
        $previous = $this->values();
        [$values, $errors] = $this->form->read($this->fields());
        if ($errors === []) {
            try {
                $values = $this->form->storeFiles($this->fields(), $values, $previous);
            } catch (UploadException $e) {
                $errors['_files'] = t($e->getMessage());
            }
        }
        if ($errors !== []) {
            return $this->page([...$previous, ...$values], $errors, 422);
        }

        // Textes traduits : une clé par langue (hero_title.fr…).
        $flat = $values;
        unset($flat['translations']);
        foreach ($values['translations'] as $lang => $texts) {
            foreach ($texts as $name => $text) {
                $flat["{$name}.{$lang}"] = $text;
            }
        }
        $this->settings->save($flat);
        $this->form->deleteReplacedFiles($this->fields(), $previous, $values);
        $this->session->flash('notice', t('admin.form.saved'));

        return $this->redirect('/admin/reglages');
    }

    /** Réglages actuels, au format des valeurs de formulaire. @return array<string, mixed> */
    private function values(): array
    {
        $all = $this->settings->all();
        $values = ['translations' => array_fill_keys(TranslatableRepository::LANGS, [])];
        foreach ($this->fields() as $field) {
            if ($field->translated) {
                foreach (TranslatableRepository::LANGS as $lang) {
                    $values['translations'][$lang][$field->name] = $all["{$field->name}.{$lang}"] ?? '';
                }
            } else {
                $values[$field->name] = match ($field->type) {
                    'toggle' => ($all[$field->name] ?? '0') === '1',
                    'image', 'pdf' => ($all[$field->name] ?? '') !== '' ? (int) $all[$field->name] : null,
                    default => $all[$field->name] ?? '',
                };
            }
        }

        return $values;
    }

    /**
     * @param array<string, mixed>  $values
     * @param array<string, string> $errors
     */
    private function page(array $values, array $errors, int $status = 200): Response
    {
        return $this->render('admin/resource/form', [
            'title' => t('admin.settings.title'),
            'labels' => ['back' => '← ' . t('admin.nav.0'), 'delete' => ''],
            'base' => url('admin.dashboard'),
            'id' => null,
            'values' => $values,
            'errors' => $errors,
            'fields' => $this->fields(),
            'hasStatus' => false,
            'action' => '/admin/reglages',
            'deleteAction' => null,
            'previewUrl' => url('home', [], 'fr'),
            'incomplete' => [],
            'intro' => t('admin.settings.intro'),
        ], status: $status);
    }
}
