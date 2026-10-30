# Back-office — Messages [P]

- **Route** : `/admin/messages`
- **Cadres** : Desktop 1440 · Mobile 390 × clair · sombre
- **Contenu** : `admin.messages`

## Sections — liste fermée, dans cet ordre

1. AdminSidebar (entrée « Messages » active, compteur non lus).
2. **AdminTopbar** : `admin.messages.title`.
3. **Deux panneaux** (desktop) :
   - **Liste (1/3)** : ChoiceChips `admin.messages.filters` (Non lus, Tous, Archivés) ; lignes = nom (`label`), type de projet (Tag), extrait (`small`, `muted`, 1 ligne), date (`caption`) ; ligne non lue marquée d'un point `accent` ; ligne sélectionnée fond `soft`.
   - **Détail (2/3)** : carte `surface` rayon `xl` — nom, email (lien `mailto:`), type de projet, date ; message complet `body` ; actions : Button `primary` `admin.messages.reply` (ouvre le client mail), Button `secondary` `admin.messages.archive`, IconButton supprimer.
4. Mobile : la liste seule ; un tap ouvre le détail en plein écran avec un lien retour.

Données de démonstration : `[Nom]`, `[email]`, `[Message]` — aucun faux message réaliste.
