-- Fichiers envoyés depuis le back-office (images, PDF).
-- Le fichier lui-même est sur le disque, dans storage/uploads/ ; la base garde ses informations.
CREATE TABLE files (
    id            serial PRIMARY KEY,
    path          text        NOT NULL UNIQUE,  -- chemin relatif à storage/uploads/, ex. 2026/11/3f9a….webp
    original_name text        NOT NULL,         -- nom d'origine, pour l'affichage et le téléchargement
    mime          text        NOT NULL,         -- type détecté à partir du contenu, pas de l'extension
    size          integer     NOT NULL CHECK (size > 0),
    width         integer,                      -- images uniquement
    height        integer,
    created_at    timestamptz NOT NULL DEFAULT now()
);
