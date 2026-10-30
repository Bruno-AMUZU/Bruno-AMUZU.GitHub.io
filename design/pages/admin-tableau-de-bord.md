# Back-office — Tableau de bord [P]

- **Route** : `/admin`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Contenu** : `admin.nav`, `admin.dashboard`

## Mise en page

AdminSidebar à gauche (mobile : remplacée par une barre haute + menu) ; zone principale fond `bg`, padding `space.8`.

## Sections — liste fermée, dans cet ordre

1. **AdminTopbar** : `admin.dashboard.title`, sans bouton.
2. **Chiffres** : 4 StatCard en grille 4 colonnes (mobile 2) — `admin.dashboard.stats` (Messages non lus, Projets publiés, Articles publiés, Brouillons). Valeurs affichées : `[N]`.
3. **Derniers messages** : carte `surface` rayon `xl`, titre `heading-2`, 3 lignes DataTable (nom, type de projet, date, StatusBadge) + lien `admin.dashboard.allMessages`.
4. **Actions rapides** : 3 Button `secondary` — `admin.dashboard.quick` (Nouveau projet, Nouvel article, Nouvelle certification).
