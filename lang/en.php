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
            'contact' => 'Contact',
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
            'subtitle' => 'Full-stack developer',
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
        'privacy' => 'Your name, email and message are only used to reply to you. They are kept for 12 months at most and never shared with third parties. You can ask for their deletion at any time.',
        'errors' => [
            'required' => 'This field is required.',
            'email' => 'Please enter a valid email address.',
            'tooLong' => 'This text is too long.',
            'throttled' => 'Too many messages sent. Please try again in an hour.',
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
            'title' => 'Full‑stack developer, I build tools that make your life easier.',
            'text' => [
                'Here is how it all started .',
                'As a kid, I loved playing mobile games. But beyond the fun of playing, one question kept coming back: how do you make a game like this? I wanted to understand what was hiding behind the screen and, above all, whether I could one day build one myself.',
                'That’s how I discovered programming.',
                'At first, I didn’t really know where to start. Which language should I learn? How did development work? I spent hours watching videos, trying different languages and exploring this world. Little by little, I understood that development was about much more than code.',
                'I found a world of people driven by creating, solving problems and pushing their limits. A world where a single bug can take hours of thinking, where you can go round in circles before finally finding the answer. And where that small victory feels huge.',
                'That’s exactly what won me over: a job where you never stop learning, where every problem is a new challenge and where you can turn an idea into something real.',
                'So I started teaching myself, with platforms like OpenClassrooms, Grafikart and countless online resources. After high school, I took the next step and joined a software development program to turn this passion into my career.',
                'Today, I’m no longer just the one wondering how things are made. I’m the one building them.',
                'I build projects that solve real problems, keep strengthening my skills, and care deeply about good practices, code quality and understanding what I build.',
                'But the job is changing. And it’s changing fast.',
                'Artificial intelligence is reshaping our habits, our tools and the way we work. Some see it as a threat, others as a great opportunity. As for me, I’d rather understand this shift and make the most of it. I keep learning new technologies, I experiment, and I already use AI in some of my projects.',
                'I don’t claim to know exactly what being a developer will look like tomorrow. No one really does.',
                'But I know why I started, and I know what makes me want to keep going: creating, understanding, solving and learning.',
                'In the end, I’m still that curious kid who wanted to know how the games on his tablet worked. The only difference is that today, I’ve made it my job.',
            ],
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
        'intro' => 'Each project tells a problem and how I solved it.',
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
        'intro' => 'Here’s what I’m experimenting with.',
        'filters' => [
            'All',
            'Back-end',
            'Front-end',
            'AI',
            'Tools',
        ],
        'empty' => 'No article in this category yet.',
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
            'text' => 'Thanks for your message, I’ll get back to you as soon as possible.',
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
                'text' => 'The only data collected comes from the contact form (name, email, project type, message). It is used only to reply to your request, kept for 12 months at most and shared with no one. The site uses no advertising cookies or analytics; only a technical cookie is set when a form is submitted. Under the GDPR, you can request access to or deletion of your data by writing to amuzuelbruno@gmail.com.',
            ],
        ],
    ],
    'notFound' => [
        'title' => 'Page not found',
        'text' => 'This page got lost along the way. Let’s go back to something that exists.',
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
