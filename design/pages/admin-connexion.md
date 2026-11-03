# Back-office — Connexion [V]

- **Route** : `/admin/connexion` (back-office en français uniquement [V])
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre ; états « erreur », « mot de passe oublié », « lien envoyé », « nouveau mot de passe » en Desktop et Mobile clair
- **Contenu** : `admin.login`, `admin.reset`

## Sections — liste fermée

1. Page fond `bg`, carte `surface` rayon `2xl` centrée, largeur 440 px (mobile 358 px), padding `space.10` (mobile `space.6`).
2. Dans la carte : logo · titre `heading-1/mobile` `admin.login.title` · Field email · Field mot de passe · Button `inverse` pleine largeur `admin.login.submit` · lien `admin.login.forgot` (`label`, centré). [V]
3. Cadre « erreur » : les deux champs en état `erreur`, un seul message `admin.login.error` sous le mot de passe. [V]

## Récupération du mot de passe [V]

Lien de réinitialisation par email ; en secours, script serveur (`php bin/reset-password.php`).
Sécurité : lien à usage unique, valable 30 min, nombre de demandes limité, message de confirmation neutre (ne révèle pas si l'adresse existe).

Même carte que la connexion (logo + titre `heading-1/mobile`) :

- **Mot de passe oublié** : texte `admin.reset.request.text` (`body`, `muted`) · Field email · Button `inverse` `admin.reset.request.submit` · lien `admin.reset.back`.
- **Lien envoyé** : texte `admin.reset.sent.text` (`body`, `muted`) · lien `admin.reset.back`.
- **Nouveau mot de passe** : Field `admin.reset.new.password` · Field `admin.reset.new.confirm` · Button `inverse` `admin.reset.new.submit`.

Pas de ChatBubble dans le back-office.
