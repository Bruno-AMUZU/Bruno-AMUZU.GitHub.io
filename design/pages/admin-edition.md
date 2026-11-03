# Back-office — Édition d'un contenu [V]

- **Route** : `/admin/{type}/{id}` — exemple maquetté : **Projet « Atelier Brume »** (fictif)
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Contenu** : `admin.edit`, `projects[0]`

## Sections — liste fermée, dans cet ordre

1. AdminSidebar (entrée « Projets » active).
2. **AdminTopbar** : lien retour `admin.edit.back` + titre du projet + actions : Button `secondary` `admin.edit.preview`, Button `primary` `admin.edit.save`.
3. **Deux colonnes** (mobile : empilées) :
   - **Principale (2/3)** : carte `surface` rayon `xl` — LangTabs FR / EN (EN marqué incomplet) ; Fields traduits du type (titre, résumé, contexte, solution, résultat en textarea).
   - **Latérale (1/3)** : cartes `surface` empilées —
     - Publication : StatusBadge + sélecteur statut + date de mise à jour ;
     - Informations : slug, année, type, liens ;
     - Stack : Tags + champ d'ajout ;
     - Médias : zone de dépôt de la capture (bordure pointillée `line`, rayon `lg`) avec aperçu.
4. En bas de la colonne principale : lien `admin.edit.delete` (texte `status.danger`).
