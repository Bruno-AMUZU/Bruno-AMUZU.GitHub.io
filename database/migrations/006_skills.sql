-- Compétences : la Stack de l'accueil et les compétences de la page À propos.

CREATE TABLE skills (
    id         serial PRIMARY KEY,
    category   text        NOT NULL DEFAULT 'front' CHECK (category IN ('front', 'back', 'tools', 'ai')),
    tech       text,        -- logo officiel (clé de public/assets/img/tech/), vide si aucun
    mono       text,        -- monogramme affiché sans logo (« CC », « MCP »…)
    position   integer     NOT NULL DEFAULT 0,
    created_at timestamptz NOT NULL DEFAULT now(),
    updated_at timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE skill_translations (
    skill_id integer NOT NULL REFERENCES skills (id) ON DELETE CASCADE,
    lang     char(2) NOT NULL CHECK (lang IN ('fr', 'en')),
    name     text    NOT NULL DEFAULT '',
    PRIMARY KEY (skill_id, lang)
);
