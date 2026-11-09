-- Formulaire de contact : Bruno cherche un stage ou une alternance, plus des projets clients.
-- Nouveaux types : internship (Stage), apprenticeship (Alternance), other (Autre).
-- Les messages des anciens types (site, app, ia) passent dans « other ».

ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_type_check;

UPDATE messages SET type = 'other' WHERE type IN ('site', 'app', 'ia');

ALTER TABLE messages
    ALTER COLUMN type SET DEFAULT 'other',
    ADD CONSTRAINT messages_type_check CHECK (type IN ('internship', 'apprenticeship', 'other'));
