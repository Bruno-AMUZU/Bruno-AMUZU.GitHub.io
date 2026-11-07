-- Services (section de l'accueil).

CREATE TABLE services (
    id           serial PRIMARY KEY,
    illustration text        NOT NULL DEFAULT 'web' CHECK (illustration IN ('web', 'apps', 'ia')),
    position     integer     NOT NULL DEFAULT 0,
    created_at   timestamptz NOT NULL DEFAULT now(),
    updated_at   timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE service_translations (
    service_id  integer NOT NULL REFERENCES services (id) ON DELETE CASCADE,
    lang        char(2) NOT NULL CHECK (lang IN ('fr', 'en')),
    title       text    NOT NULL DEFAULT '',
    description text    NOT NULL DEFAULT '',
    PRIMARY KEY (service_id, lang)
);
