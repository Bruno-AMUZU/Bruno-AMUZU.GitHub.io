-- Certifications (page À propos).

CREATE TABLE certifications (
    id           serial PRIMARY KEY,
    obtained_on  date,                                              -- date d'obtention
    organization text        NOT NULL DEFAULT '',                   -- organisme (OpenClassrooms…)
    file_id      integer     REFERENCES files (id) ON DELETE SET NULL, -- PDF du certificat
    url          text,                                              -- ou lien vers le certificat en ligne
    position     integer     NOT NULL DEFAULT 0,
    created_at   timestamptz NOT NULL DEFAULT now(),
    updated_at   timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE certification_translations (
    certification_id integer NOT NULL REFERENCES certifications (id) ON DELETE CASCADE,
    lang             char(2) NOT NULL CHECK (lang IN ('fr', 'en')),
    title            text    NOT NULL DEFAULT '',
    PRIMARY KEY (certification_id, lang)
);
