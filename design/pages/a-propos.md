# À propos [V]

- **Routes** : `/fr/a-propos`, `/en/about`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Contenu** : `common`, `about`, `stack`, `timeline`, `certifications`
- **Rôle** : rassurer (recruteurs surtout) — parcours, compétences complètes, certifications, CV.

## Sections — liste fermée, dans cet ordre

1. **Header** [V]
2. **Présentation** [V]
   - Carte `surface`, rayon `2xl`, deux colonnes (mobile : empilées, photo en premier).
   - Gauche : titre `display-l` `about.intro.title`, texte `body-l` `about.intro.text`, Button `primary` `about.intro.cv` (téléchargement du CV) + Button `secondary` `about.intro.contact` → page `/contact`.
   - Droite : photo de Bruno, rayon `xl`, ratio 4:5. Tant qu'elle n'est pas fournie : bloc `soft` « [Photo à fournir] ». [V]
3. **Parcours** [V]
   - SectionHeader `about.timeline.title`.
   - Frise verticale : chaque entrée de `timeline` = carte `surface` rayon `xl` avec période (`small`, `muted`), intitulé (`heading-2`), structure + lieu (`body`), description (`body`, `muted`). Pill indiquant le type (Formation / Expérience).
4. **Compétences** [V]
   - SectionHeader `about.skills.title`.
   - Mêmes StackCard que l'accueil (même source `stack`). [P : version détaillée possible plus tard]
5. **Certifications** [V]
   - SectionHeader `about.certifications.title`.
   - Grille 3 colonnes (mobile 1) de cartes `surface` rayon `xl` : date (`small`, `muted`), titre (`heading-3`), organisme (`body`, `muted`), lien « Voir le certificat → ». Toutes identiques. Ordre : plus récente d'abord.
6. **Footer** [V]

## Flottant

ChatBubble. [V]

## Interdits

Barres de progression ou pourcentages de compétence, emojis.
