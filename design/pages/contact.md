# Contact [V]

Page dédiée ; il n'y a **pas** de section Contact sur l'accueil.

- **Routes** : `/fr/contact`, `/en/contact`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre, + cadre « Message envoyé » (desktop et mobile)
- **Contenu** : `common`, `form`, `contactPage`

## Sections — liste fermée, dans cet ordre

1. **Header** [V]
2. **ContactBlock** [V] : accroche `contactPage.title`, formulaire avec les libellés de `form` (+ mention `form.privacy` sous le bouton, `caption`, `muted`).
3. **Footer** [V]

## Cadre « Message envoyé » [V]

La carte formulaire est remplacée par une carte `surface` : `contactPage.success.title` (`heading-1`) et `contactPage.success.text` (`body`, `muted`).

## Flottant

ChatBubble. [V]

## Interdits

CV, téléphone.
