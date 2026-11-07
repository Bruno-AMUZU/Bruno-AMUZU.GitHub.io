-- Messages reçus par le formulaire de contact. Enregistrés d'abord ici, l'e-mail n'est qu'une notification.

CREATE TABLE messages (
    id         serial PRIMARY KEY,
    name       text        NOT NULL,
    email      text        NOT NULL,
    type       text        NOT NULL DEFAULT 'other' CHECK (type IN ('site', 'app', 'ia', 'other')),
    body       text        NOT NULL,
    lang       char(2)     NOT NULL DEFAULT 'fr',
    status     text        NOT NULL DEFAULT 'unread' CHECK (status IN ('unread', 'read', 'archived')),
    created_at timestamptz NOT NULL DEFAULT now()
);

CREATE INDEX messages_status_created ON messages (status, created_at DESC);
