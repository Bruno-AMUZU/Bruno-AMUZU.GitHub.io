-- Le Lab ne parle plus seulement d'IA : nouvelles catégories Back-end, Front-end, IA, Outils.
-- Les articles des anciennes catégories (context, harness, rag) passent dans « ia ».

ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_category_check;

UPDATE articles SET category = 'ia' WHERE category IN ('context', 'harness', 'rag');

ALTER TABLE articles
    ALTER COLUMN category SET DEFAULT 'back',
    ADD CONSTRAINT articles_category_check CHECK (category IN ('back', 'front', 'ia', 'tools'));
