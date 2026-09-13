# ALODO Metric

**Le diagnostic de structuration pour MPME du Bénin** — 8 dimensions évaluées en 4 minutes, gratuit et 100% confidentiel. Un score vivant de 0 à 100, des recommandations concrètes, et une règle d'or : signaler une faiblesse **sans jamais décourager**.

## Démarrage rapide

### Backend (Laravel)
```bash
cd backend
composer install
php artisan migrate --seed
php artisan serve          # http://127.0.0.1:8000
```

### Frontend (Angular)
```bash
cd metric-frontend
npm install
npx ng serve               # http://localhost:4200
```

## Accès admin (en clair)

L'écran de connexion est **centré** horizontalement pour respirer symétriquement.

- **URL de l'écran de connexion** : `http://localhost:4200/admin`
- **Compte de démo principal** : `demo@metric` · mot de passe : `demo123`
- **Compte admin de test** : `admin@metric` · mot de passe : `admin123`

Sur l'admin, deux onglets :
1. **Résultats** — rangée de cartes globales (nombre de diagnostics, score moyen…), classement des MPME (barres 0-100), tableau des 8 dimensions avec les points faibles en brique
2. **Questions** — liste éditable des questions (texte, ordre, score par option), avec skeletons pulsants pendant le chargement

## Fonctionnalités UX

| Lot | Fonctionnalité |
|---|---|
| Jauge du résultat | compteur 0 → score + anneau orange (900ms, ease-out), puis cascade des 8 barres |
| Règle "sans humilier" (score < 50) | jauge toujours orange, "Votre premier levier de progrès", l'action recommandée remonte en tête, barres en gris neutre (jamais rouge criard) |
| Compteurs animés (Lot A) | stats de l'accueil + preuve sociale vivante : 0 → valeur au scroll (IntersectionObserver) |
| Micro-interactions (Lot C) | `.btn:active` scale(0.98) — enfoncement tactile de tous les boutons |
| Skeletons (Lot D) | classe `.skeleton` : bloc gris pulsant pendant les chargements |
| Responsive | très petits écrans (< 380px) pris en charge |
| Accessibilité | `prefers-reduced-motion` : toutes les animations (jauge, compteurs, skeletons) sont désactivées |

## Règle d'accessibilité
Toutes les animations (jauge, compteurs, skeletons, cascade) sont **désactivées** si l'utilisateur a `prefers-reduced-motion: reduce` — les valeurs s'affichent directement.

## API publique

- `GET /api/public/stats` — nombre de diagnostics (preuve sociale vivante)
- `GET /api/public/questions` — liste des questions du diagnostic
- `POST /api/public/diagnostics` — soumission d'un diagnostic (retourne le score + token)

## Structure
```
metric-frontend/src/app/components/
├── home/       → accueil (stats animées, preuve sociale)
├── questions/  → parcours du diagnostic
├── result/     → jauge animée + cascade + règle sans-humilier
└── admin/      → écran de connexion + 2 onglets (résultats, questions)
```
