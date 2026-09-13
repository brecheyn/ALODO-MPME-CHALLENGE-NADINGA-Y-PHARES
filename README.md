# ALODO Metric

ALODO Metric évalue la solidité financière d'une MPME. L'utilisateur passe par un parcours en trois étapes : une intro, un questionnaire, puis un résultat avec un score, ses points forts, ses points faibles et une recommandation.

Le questionnaire n'est pas figé dans le code : un back-office permet d'ajouter ou supprimer des questions à tout moment, sans redéploiement. C'est ce qui rend le diagnostic évolutif.

## URLs

- Frontend Vercel: https://alodo-metic.vercel.app
- Backend Render: https://alodo-metic.onrender.com
- API publique: https://alodo-metic.onrender.com/api/beta
- Back-office: https://alodo-metic.vercel.app/admin

## Choix produit

Le diagnostic se concentre sur la dimension **Finance** : trésorerie, marges, gestion des dépenses, dettes et créances. Quelques questions **Commercial** sont ajoutées en complément, parce que le niveau de vente influence directement la trésorerie.

Les autres dimensions du programme ALODO MPME (Formalisation, Comptabilité, Digitalisation, Opérations, RH, Préparation au financement) ne sont pas traitées dans ce prototype, pour rester dans le délai de 3 jours et livrer quelque chose de cohérent plutôt que d'être incomplet partout.

## Structure

```text
.
├── metric-frontend/     # Application Angular
├── metric_backend/      # API Laravel
├── Dockerfile           # Image Render depuis la racine du repo
├── render.yaml          # Blueprint Render
└── README.md
```

## Stack

- Frontend: Angular 22
- Backend: Laravel 13, Sanctum
- Base de donnees: MySQL Aiven
- Frontend hosting: Vercel
- Backend hosting: Render avec Docker PHP 8.4 Apache

## Fonctionnalités

- Parcours complet : intro → questionnaire → résultat
- Calcul du score global et du score par dimension (Finance / Commercial)
- Points forts, points faibles et recommandation
- Back-office authentifié pour gérer les questions (ajout, suppression)
- API publique (`/api/beta`) utilisable indépendamment du frontend

## Demarrage Local

Backend:

```bash
cd metric_backend
composer install
php artisan config:clear
php artisan migrate
php artisan db:seed
php artisan serve
```

Frontend:

```bash
cd metric-frontend
npm install
npm run start
```

URLs locales:

- Frontend: http://localhost:4200
- Backend: http://localhost:8000
- API locale: http://localhost:8000/api/beta

## Variables D'environnement

Le fichier `metric_backend/.env` reste local et ne doit jamais etre commit. Les valeurs de production doivent etre configurees dans Render.

Variables backend principales:

```env
APP_URL=https://alodo-metic.onrender.com
FRONTEND_URL=https://alodo-metic.vercel.app

```

## Deploiement

Vercel:

```text
Root Directory: metric-frontend
Build Command: npm run build
Output Directory: dist/metric-frontend/browser
```

Render:

- Utiliser `render.yaml` a la racine.
- Le service utilise `Dockerfile` a la racine.
- Le conteneur copie `metric_backend/`.
- Au demarrage, Render lance les migrations et les seeders.

Apres deploiement Render, verifier:

```text
https://alodo-metic.onrender.com/api/beta/questions
```

La reponse doit contenir les questions du diagnostic.

## Administration

Back-office:

```text
https://alodo-metic.vercel.app/admin
```

Comptes de démonstration fournis sur demande (retirés du README pour la mise en beta).

## Branches

La branche de deploiement active est:

```text
main
```

Les anciennes branches `metric_backend` et `metric_frontend` ont servi au developpement separe. Le code deployable complet a ete fusionne dans `main`.

## Limites

- Seulement 2 dimensions sur les 8 sont couvertes (Finance et Commercial)
- Pas de suivi dans le temps entre plusieurs passages du diagnostic
- Le parcours est le même pour tout le monde, il ne s'adapte pas selon le profil de l'entreprise
- Sécurité du back-office simplifiée pour la beta, à durcir avant une vraie mise en production
- Pas de tests automatisés pour l'instant

## Améliorations

- Faire évoluer le parcours selon le profil déclaré de l'entreprise
- Ajouter un suivi dans le temps entre plusieurs passages
- Étendre progressivement à d'autres dimensions (Digitalisation en particulier)
- Export du résultat en PDF
- Renforcer la sécurité de l'admin (rotation des mots de passe, rôles différenciés)
