-- Réglages du site : une valeur par clé (disponibilité, e-mail, liens, CV, photo, textes du hero).
-- Les textes traduits utilisent une clé par langue : hero_title.fr, hero_title.en.

CREATE TABLE settings (
    key        text PRIMARY KEY,
    value      text        NOT NULL DEFAULT '',
    updated_at timestamptz NOT NULL DEFAULT now()
);
