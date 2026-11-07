<?php

/*
 * Textes de l'interface en anglais.
 * Repris de design/content.en.json (les contenus gérés depuis le back-office n'y figurent pas).
 * Utilisation dans une vue : <?= e(t('home.hero.title')) ?>
 * Une clé absente ici est cherchée dans lang/fr.php (le back-office n'existe qu'en français).
 */

return [
    'common' => [
        'logo' => 'bruno amuzu',
        'skip' => 'Skip to content',
        'nav' => [
            'services' => 'Services',
            'stack' => 'Stack',
            'projects' => 'Projects',
            'lab' => 'Lab',
            'about' => 'About',
        ],
        'cta' => 'Contact me',
        'lang' => [
            'fr' => 'FR',
            'en' => 'EN',
        ],
        'theme' => [
            'toLight' => 'Switch to light theme',
            'toDark' => 'Switch to dark theme',
        ],
        'menu' => [
            'open' => 'Open menu',
            'close' => 'Close menu',
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
                    'label' => 'Legal notice',
                    'url' => '/en/legal-notice',
                ],
            ],
        ],
    ],
    'home' => [
        'hero' => [
            'status' => 'Looking for an internship or work-study position',
            'title' => 'Websites and web tools that move your business forward.',
            'subtitle' => 'Web developer · PHP · Applied AI',
            'ctaPrimary' => 'Contact me',
            'ctaSecondary' => 'See projects',
        ],
        'services' => [
            'title' => 'Services',
            'diagram' => [
                'Context',
                'Agent',
                'Result',
            ],
        ],
        'stack' => [
            'title' => 'Stack',
            'count' => '{n} tools',
        ],
        'projects' => [
            'title' => 'Projects',
            'link' => 'All projects →',
        ],
        'lab' => [
            'title' => 'AI Lab',
            'link' => 'All notes →',
        ],
    ],
    'form' => [
        'name' => 'Name',
        'namePlaceholder' => 'Your name',
        'email' => 'Email',
        'emailPlaceholder' => 'you@example.com',
        'type' => 'Project type',
        'types' => [
            'Website',
            'Application',
            'AI',
            'Other',
        ],
        'message' => 'Message',
        'messagePlaceholder' => 'Tell me about your project…',
        'submit' => 'Send message',
        'privacy' => '[À FOURNIR] Short privacy notice (how form data is used).',
        'errors' => [
            'required' => 'This field is required.',
            'email' => 'Please enter a valid email address.',
        ],
    ],
    'chat' => [
        'open' => 'Open the AI assistant',
        'close' => 'Close the assistant',
        'title' => 'AI assistant',
        'subtitle' => 'Answers from this site’s content',
        'placeholder' => 'Your question…',
        'send' => 'Send',
    ],
    'about' => [
        'kinds' => [
            'education' => 'Education',
            'experience' => 'Experience',
        ],
        'intro' => [
            'title' => '[À FOURNIR] Introduction title',
            'text' => '[À FOURNIR] 3-4 sentence introduction.',
            'cv' => 'Download my resume',
            'contact' => 'Contact me',
        ],
        'timeline' => [
            'title' => 'Background',
        ],
        'skills' => [
            'title' => 'Skills',
        ],
        'certifications' => [
            'title' => 'Certifications',
            'link' => 'View certificate →',
        ],
    ],
    'projectsPage' => [
        'title' => 'Projects',
        'intro' => '[À FOURNIR] One-sentence introduction.',
        'filters' => [
            'All',
            'Websites',
            'Applications',
            'AI',
        ],
        'empty' => 'No project in this category yet.',
    ],
    'projectPage' => [
        'back' => '← All projects',
        'demo' => 'Visit site',
        'code' => 'View code',
        'context' => 'Context',
        'solution' => 'What I built',
        'result' => 'Outcome',
        'next' => 'Next project',
    ],
    'labPage' => [
        'title' => 'AI Lab',
        'intro' => '[À FOURNIR] One-sentence introduction.',
        'filters' => [
            'All',
            'Context engineering',
            'Harness engineering',
            'RAG',
        ],
    ],
    'articlePage' => [
        'back' => '← All notes',
        'related' => 'Read next',
        'video' => [
            'play' => 'Play the video',
            'duration' => '[DURÉE]',
        ],
    ],
    'contactPage' => [
        'success' => [
            'title' => 'Message sent.',
            'text' => '[À FOURNIR] Confirmation sentence.',
        ],
        'title' => 'Got a project in mind? Let’s talk.',
    ],
    'legal' => [
        'title' => 'Legal notice',
        'blocks' => [
            [
                'title' => 'Publisher',
                'text' => 'Bruno Amuzu — amuzuelbruno@gmail.com',
            ],
            [
                'title' => 'Hosting',
                'text' => '[À FOURNIR]',
            ],
            [
                'title' => 'Personal data',
                'text' => '[À FOURNIR]',
            ],
        ],
    ],
    'notFound' => [
        'title' => 'Page not found',
        'text' => '[À FOURNIR] One sentence.',
        'cta' => 'Back to home',
    ],
    // Ajouté pour le site (absent de la spec) : titres et messages techniques.
    'pages' => [
        'home' => 'Home',
    ],
    'projects' => [
        'types' => [
            'site' => 'Website',
            'app' => 'Application',
            'ia' => 'AI',
        ],
    ],
    'skills' => [
        'categories' => [
            'front' => 'Front-end',
            'back' => 'Back-end',
            'tools' => 'Tools & infra',
            'ai' => 'AI',
        ],
    ],
    'upload' => [
        'type' => 'This file type is not allowed.',
        'tooBig' => 'File too large.',
        'failed' => 'The upload failed.',
    ],
    'errors' => [
        '403' => 'Access denied.',
        '405' => 'Method not allowed.',
        '500' => 'Something went wrong. Please try again shortly.',
    ],
];
