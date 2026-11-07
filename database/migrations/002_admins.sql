-- Comptes du back-office et sécurité de la connexion.

CREATE TABLE admins (
    id            serial PRIMARY KEY,
    email         text        NOT NULL,
    name          text        NOT NULL DEFAULT '',
    password_hash text        NOT NULL,                -- password_hash() : jamais le mot de passe lui-même
    last_login_at timestamptz,
    created_at    timestamptz NOT NULL DEFAULT now()
);
-- Une adresse = un compte, sans tenir compte des majuscules.
CREATE UNIQUE INDEX admins_email_unique ON admins (lower(email));

-- Liens « mot de passe oublié » : on garde l'empreinte du jeton, pas le jeton.
CREATE TABLE password_resets (
    id         serial PRIMARY KEY,
    admin_id   integer     NOT NULL REFERENCES admins (id) ON DELETE CASCADE,
    token_hash text        NOT NULL UNIQUE,
    expires_at timestamptz NOT NULL,
    used_at    timestamptz,
    created_at timestamptz NOT NULL DEFAULT now()
);

-- Compteur générique de tentatives (connexion, demandes de lien, formulaire de contact).
CREATE TABLE throttle_hits (
    id         bigserial PRIMARY KEY,
    key        text        NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX throttle_hits_key_time ON throttle_hits (key, created_at);
