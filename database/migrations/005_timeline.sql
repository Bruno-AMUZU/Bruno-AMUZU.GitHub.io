-- Parcours : formations et expériences (page À propos).

CREATE TABLE timeline_entries (
    id           serial PRIMARY KEY,
    kind         text        NOT NULL DEFAULT 'education' CHECK (kind IN ('education', 'experience')),
    start_date   date,                -- mois de début (stocké au 1er du mois)
    end_date     date,                -- mois de fin ; vide = en cours
    organization text        NOT NULL DEFAULT '',
    place        text        NOT NULL DEFAULT '',
    position     integer     NOT NULL DEFAULT 0,
    created_at   timestamptz NOT NULL DEFAULT now(),
    updated_at   timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE timeline_entry_translations (
    timeline_entry_id integer NOT NULL REFERENCES timeline_entries (id) ON DELETE CASCADE,
    lang              char(2) NOT NULL CHECK (lang IN ('fr', 'en')),
    title             text    NOT NULL DEFAULT '', -- intitulé
    description       text    NOT NULL DEFAULT '',
    PRIMARY KEY (timeline_entry_id, lang)
);
