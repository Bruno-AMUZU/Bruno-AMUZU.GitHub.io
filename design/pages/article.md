# Article du Lab [V]

- **Routes** : `/fr/lab/{slug}`, `/en/lab/{slug}`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre (article sans vidéo) + état « avec vidéo » Desktop et Mobile en clair
- **Contenu** : `common`, `articlePage`, `lab[0]`

## Sections — liste fermée, dans cet ordre

1. **Header**
2. **Retour** : lien `articlePage.back`.
3. **En-tête** (colonne de lecture 720 px centrée) : Pill `accent` catégorie, titre `display-l`, date + temps de lecture (`small`, `muted`).
4. **Vidéo** (facultative) [V] — uniquement si l'article en a une, comme un cours OpenClassrooms : la vidéo montre et explique, le texte suit en dessous.
   - Vidéos hébergées sur **YouTube** (lecteur intégré, mode sans cookie `youtube-nocookie.com`). Composant VideoPlayer pleine largeur de la colonne, ratio 16:9 (720 × 405 ; mobile 358 × 201), rayon `lg`.
   - Absente → la section n'existe pas (cadres « Article » standards).
5. **Corps** (colonne 720 px) : texte `body-l` ; intertitres `heading-2` ; bloc de code (fond `inv`, texte `invInk`, style `code` (Consolas / monospace système), rayon `lg`, padding `space.6`) ; citation (bordure gauche interdite → fond `soft`, rayon `lg`). [V]
   Texte de démonstration : `articlePage.sample`.
6. **Articles liés** : 2 cartes Lab.
7. **Footer**

## Flottant

ChatBubble.
