-- Projets (design/pages/admin-liste.md → Champs par type).

CREATE TABLE projects (
    id         serial PRIMARY KEY,
    slug       text        NOT NULL UNIQUE,
    year       integer,
    type       text        NOT NULL DEFAULT 'site' CHECK (type IN ('site', 'app', 'ia')),
    image_id   integer     REFERENCES files (id) ON DELETE SET NULL, -- capture principale
    github_url text,
    demo_url   text,
    status     text        NOT NULL DEFAULT 'draft' CHECK (status IN ('draft', 'published')),
    position   integer     NOT NULL DEFAULT 0,
    created_at timestamptz NOT NULL DEFAULT now(),
    updated_at timestamptz NOT NULL DEFAULT now()
);

-- Textes dans chaque langue : une ligne par projet et par langue.
CREATE TABLE project_translations (
    project_id integer NOT NULL REFERENCES projects (id) ON DELETE CASCADE,
    lang       char(2) NOT NULL CHECK (lang IN ('fr', 'en')),
    title      text    NOT NULL DEFAULT '',
    summary    text    NOT NULL DEFAULT '',
    context    text    NOT NULL DEFAULT '', -- étude de cas : contexte
    solution   text    NOT NULL DEFAULT '', -- ce que j'ai construit
    result     text    NOT NULL DEFAULT '', -- résultat
    PRIMARY KEY (project_id, lang)
);

-- Technos du projet (Tags), dans l'ordre de saisie.
CREATE TABLE project_technologies (
    project_id integer NOT NULL REFERENCES projects (id) ON DELETE CASCADE,
    name       text    NOT NULL,
    position   integer NOT NULL,
    PRIMARY KEY (project_id, position)
);

-- Galerie de la page projet.
CREATE TABLE project_images (
    project_id integer NOT NULL REFERENCES projects (id) ON DELETE CASCADE,
    file_id    integer NOT NULL REFERENCES files (id) ON DELETE CASCADE,
    position   integer NOT NULL,
    PRIMARY KEY (project_id, file_id)
);

CREATE INDEX projects_status_position ON projects (status, position);
