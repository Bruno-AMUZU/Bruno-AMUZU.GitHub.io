<?php

/*
 * Textes de l'interface en français.
 * Repris de design/content.fr.json (les contenus gérés depuis le back-office n'y figurent pas).
 * Utilisation dans une vue : <?= e(t('home.hero.title')) ?>
 */

return [
    'common' => [
        'logo' => 'bruno amuzu',
        'skip' => 'Aller au contenu',
        'nav' => [
            'services' => 'Services',
            'stack' => 'Stack',
            'projects' => 'Projets',
            'lab' => 'Lab',
            'about' => 'À propos',
        ],
        'cta' => 'Me contacter',
        'lang' => [
            'fr' => 'FR',
            'en' => 'EN',
        ],
        'theme' => [
            'toLight' => 'Passer en thème clair',
            'toDark' => 'Passer en thème sombre',
        ],
        'menu' => [
            'open' => 'Ouvrir le menu',
            'close' => 'Fermer le menu',
        ],
        'footer' => [
            'copyright' => '© 2026 Bruno Amuzu',
            'links' => [
                [
                    'label' => 'GitHub',
                    'url' => 'https://github.com/Bruno-AMUZU',
                ],
                [
                    'label' => 'LinkedIn',
                    'url' => 'https://www.linkedin.com/in/bruno-amuzu',
                ],
                [
                    'label' => 'X',
                    'url' => 'https://x.com/bruno_amuzu',
                ],
                [
                    'label' => 'amuzuelbruno@gmail.com',
                    'url' => 'mailto:amuzuelbruno@gmail.com',
                ],
                [
                    'label' => 'Mentions légales',
                    'url' => '/fr/mentions-legales',
                ],
            ],
        ],
    ],
    'home' => [
        'hero' => [
            'status' => 'En recherche d’un stage ou d’une alternance',
            'title' => 'Des sites et des outils web qui font avancer votre activité.',
            'subtitle' => 'Développeur web · PHP · IA appliquée',
            'ctaPrimary' => 'Me contacter',
            'ctaSecondary' => 'Voir les projets',
        ],
        'services' => [
            'title' => 'Services',
            'diagram' => [
                'Contexte',
                'Agent',
                'Résultat',
            ],
        ],
        'stack' => [
            'title' => 'Stack',
            'count' => '{n} outils',
        ],
        'projects' => [
            'title' => 'Projets',
            'link' => 'Tous les projets →',
        ],
        'lab' => [
            'title' => 'Lab IA',
            'link' => 'Toutes les notes →',
        ],
    ],
    'form' => [
        'name' => 'Nom',
        'namePlaceholder' => 'Votre nom',
        'email' => 'Email',
        'emailPlaceholder' => 'vous@exemple.fr',
        'type' => 'Type de projet',
        'types' => [
            'Site vitrine',
            'Application',
            'IA',
            'Autre',
        ],
        'message' => 'Message',
        'messagePlaceholder' => 'Parlez-moi de votre projet…',
        'submit' => 'Envoyer le message',
        'privacy' => '[À FOURNIR] Mention RGPD courte (usage des données du formulaire).',
        'errors' => [
            'required' => 'Ce champ est obligatoire.',
            'email' => 'Adresse email invalide.',
        ],
    ],
    'chat' => [
        'open' => 'Ouvrir l’assistant IA',
        'close' => 'Fermer l’assistant',
        'title' => 'Assistant IA',
        'subtitle' => 'Répond à partir du contenu du site',
        'placeholder' => 'Votre question…',
        'send' => 'Envoyer',
    ],
    'about' => [
        'intro' => [
            'title' => '[À FOURNIR] Titre de présentation',
            'text' => '[À FOURNIR] Présentation en 3-4 phrases.',
            'cv' => 'Télécharger mon CV',
            'contact' => 'Me contacter',
        ],
        'timeline' => [
            'title' => 'Parcours',
        ],
        'skills' => [
            'title' => 'Compétences',
        ],
        'certifications' => [
            'title' => 'Certifications',
            'link' => 'Voir le certificat →',
        ],
    ],
    'projectsPage' => [
        'title' => 'Projets',
        'intro' => '[À FOURNIR] Une phrase d’introduction.',
        'filters' => [
            'Tous',
            'Sites',
            'Applications',
            'IA',
        ],
    ],
    'projectPage' => [
        'back' => '← Tous les projets',
        'demo' => 'Voir le site',
        'code' => 'Voir le code',
        'context' => 'Contexte',
        'solution' => 'Ce que j’ai construit',
        'result' => 'Résultat',
        'next' => 'Projet suivant',
    ],
    'labPage' => [
        'title' => 'Lab IA',
        'intro' => '[À FOURNIR] Une phrase d’introduction.',
        'filters' => [
            'Tout',
            'Context engineering',
            'Harness engineering',
            'RAG',
        ],
    ],
    'articlePage' => [
        'back' => '← Toutes les notes',
        'related' => 'À lire aussi',
        'video' => [
            'play' => 'Lire la vidéo',
            'duration' => '[DURÉE]',
        ],
    ],
    'contactPage' => [
        'success' => [
            'title' => 'Message envoyé.',
            'text' => '[À FOURNIR] Phrase de confirmation.',
        ],
        'title' => 'Un projet en tête ? Parlons-en.',
    ],
    'legal' => [
        'title' => 'Mentions légales',
        'blocks' => [
            [
                'title' => 'Éditeur du site',
                'text' => 'Bruno Amuzu — amuzuelbruno@gmail.com',
            ],
            [
                'title' => 'Hébergement',
                'text' => '[À FOURNIR]',
            ],
            [
                'title' => 'Données personnelles',
                'text' => '[À FOURNIR]',
            ],
        ],
    ],
    'notFound' => [
        'title' => 'Page introuvable',
        'text' => '[À FOURNIR] Une phrase.',
        'cta' => 'Retour à l’accueil',
    ],
    // Ajouté pour le site (absent de la spec) : titres et messages techniques.
    'pages' => [
        'home' => 'Accueil',
    ],
    'upload' => [
        'type' => 'Ce type de fichier n’est pas accepté.',
        'tooBig' => 'Fichier trop lourd.',
        'failed' => 'L’envoi du fichier a échoué.',
    ],
    'errors' => [
        '403' => 'Accès refusé.',
        '405' => 'Méthode non autorisée.',
        '500' => 'Une erreur est survenue. Réessayez dans un instant.',
    ],
    'admin' => [
        'nav' => [
            'Tableau de bord',
            'Projets',
            'Certifications',
            'Parcours',
            'Compétences',
            'Services',
            'Lab',
            'Messages',
            'Réglages',
        ],
        'viewSite' => 'Voir le site',
        'logout' => 'Déconnexion',
        'login' => [
            'title' => 'Connexion',
            'email' => 'Email',
            'password' => 'Mot de passe',
            'submit' => 'Se connecter',
            'error' => 'Email ou mot de passe incorrect.',
            'passwordPlaceholder' => '••••••••',
            'throttled' => 'Trop de tentatives. Réessayez dans quelques minutes.',
            'forgot' => 'Mot de passe oublié ?',
        ],
        'dashboard' => [
            'title' => 'Tableau de bord',
            'stats' => [
                'Messages non lus',
                'Projets publiés',
                'Articles publiés',
                'Brouillons',
            ],
            'latest' => 'Derniers messages',
            'allMessages' => 'Tous les messages →',
            'quick' => [
                'Nouveau projet',
                'Nouvel article',
                'Nouvelle certification',
            ],
        ],
        'list' => [
            'title' => 'Projets',
            'new' => '+ Nouveau',
            'search' => 'Rechercher…',
            'filters' => [
                'Tous',
                'Publiés',
                'Brouillons',
            ],
            'empty' => 'Aucun projet pour l’instant.',
            'status' => [
                'published' => 'Publié',
                'draft' => 'Brouillon',
                'unread' => 'Non lu',
                'read' => 'Lu',
                'archived' => 'Archivé',
            ],
            // Ajouté pour le site (absent de la spec)
            'noResult' => 'Aucun résultat pour cette recherche.',
            'reorderHelp' => 'Glissez les lignes par la poignée pour changer l’ordre d’affichage sur le site.',
        ],
        // Ajouté pour le site (absent de la spec) : formulaires génériques du back-office
        'form' => [
            'hasErrors' => 'Le contenu n’a pas été enregistré : corrigez les champs signalés.',
            'confirmDelete' => 'Supprimer « {title} » ? Cette action est définitive.',
            'tagsHelp' => 'Entrée ou virgule pour ajouter.',
            'dropPdf' => 'Glisser un PDF ou parcourir',
            'removeFile' => 'Retirer le fichier',
            'keep' => 'Garder',
            'addImages' => 'Ajouter des images',
            'saved' => 'Modifications enregistrées.',
            'deleted' => 'Contenu supprimé.',
            'errors' => [
                'required' => 'Ce champ est obligatoire.',
                'max' => '{n} caractères maximum.',
                'url' => 'Adresse invalide (elle doit commencer par https://).',
                'email' => 'Adresse email invalide.',
                'number' => 'Nombre entier attendu.',
                'date' => 'Date invalide.',
                'select' => 'Choix invalide.',
                'slug' => 'Minuscules, chiffres et tirets uniquement (ex. atelier-brume).',
                'slugTaken' => 'Ce slug est déjà utilisé.',
            ],
        ],
        'edit' => [
            'back' => '← Projets',
            'preview' => 'Prévisualiser',
            'save' => 'Enregistrer',
            'delete' => 'Supprimer ce projet',
            'panels' => [
                'Publication',
                'Informations',
                'Stack',
                'Médias',
            ],
            'incomplete' => 'Traduction incomplète',
            'fields' => [
                'title' => 'Titre',
                'summary' => 'Résumé',
                'context' => 'Contexte',
                'solution' => 'Solution',
                'result' => 'Résultat',
                'slug' => 'Slug',
                'year' => 'Année',
                'type' => 'Type',
                'github' => 'Lien GitHub',
                'demo' => 'Lien démo',
                'status' => 'Statut',
                'image' => 'Capture',
                'updated' => 'Mis à jour le',
            ],
            'dropzone' => 'Glisser une image ou parcourir',
            'addTech' => 'Ajouter une techno…',
        ],
        'messages' => [
            'title' => 'Messages',
            'filters' => [
                'Non lus',
                'Tous',
                'Archivés',
            ],
            'reply' => 'Répondre',
            'archive' => 'Archiver',
            'demo' => [
                'name' => '[Nom]',
                'email' => '[email]',
                'type' => 'Site vitrine',
                'message' => '[Message]',
                'date' => '[DATE]',
            ],
        ],
        'reset' => [
            'back' => '← Retour à la connexion',
            'request' => [
                'title' => 'Mot de passe oublié',
                'text' => 'Saisissez votre email : vous recevrez un lien pour choisir un nouveau mot de passe.',
                'submit' => 'Envoyer le lien',
            ],
            'sent' => [
                'title' => 'Vérifiez vos emails',
                'text' => 'Si l’adresse correspond à un compte, un lien de réinitialisation vient d’être envoyé. Il est valable 30 minutes.',
            ],
            'new' => [
                'title' => 'Nouveau mot de passe',
                'password' => 'Nouveau mot de passe',
                'confirm' => 'Confirmer le mot de passe',
                'submit' => 'Enregistrer',
                // Ajouté pour le site (absent de la spec)
                'tooShort' => '{n} caractères minimum.',
                'mismatch' => 'Les deux mots de passe sont différents.',
                'invalid' => 'Ce lien n’est plus valable (déjà utilisé ou expiré). Demandez-en un nouveau.',
                'done' => 'Mot de passe enregistré. Vous pouvez vous connecter.',
            ],
            'mail' => [
                'subject' => 'Réinitialisation de votre mot de passe',
                'body' => "Bonjour,\n\nPour choisir un nouveau mot de passe pour le back-office, ouvrez ce lien (valable {minutes} minutes, utilisable une seule fois) :\n\n{link}\n\nSi vous n’êtes pas à l’origine de cette demande, ignorez cet e-mail : votre mot de passe reste inchangé.",
            ],
        ],
    ],
];
