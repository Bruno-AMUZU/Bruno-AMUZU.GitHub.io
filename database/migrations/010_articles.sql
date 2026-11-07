-- Articles du Lab (design/pages/admin-liste.md → Champs par type).

CREATE TABLE articles (
    id           serial PRIMARY KEY,
    slug         text        NOT NULL UNIQUE,
    category     text        NOT NULL DEFAULT 'context' CHECK (category IN ('context', 'harness', 'rag')),
    published_on date,
    reading_time integer     CHECK (reading_time IS NULL OR reading_time > 0), -- minutes
    video_url    text,                                                         -- lien YouTube facultatif
    status       text        NOT NULL DEFAULT 'draft' CHECK (status IN ('draft', 'published')),
    position     integer     NOT NULL DEFAULT 0,
    created_at   timestamptz NOT NULL DEFAULT now(),
    updated_at   timestamptz NOT NULL DEFAULT now()
);

CREATE TABLE article_translations (
    article_id integer NOT NULL REFERENCES articles (id) ON DELETE CASCADE,
    lang       char(2) NOT NULL CHECK (lang IN ('fr', 'en')),
    title      text    NOT NULL DEFAULT '',
    excerpt    text    NOT NULL DEFAULT '',
    content    text    NOT NULL DEFAULT '', -- Markdown
    PRIMARY KEY (article_id, lang)
);

CREATE INDEX articles_status_date ON articles (status, published_on DESC);
