# ALODO Metric

ALODO Metric est une application de diagnostic pour aider les MPME a evaluer leur structuration financiere. Le parcours public pose une serie de questions, calcule un score de maturite, puis retourne un resultat avec forces, priorites et recommandations.

## URLs

- Frontend Vercel: https://alodo-metic.vercel.app
- Backend Render: https://alodo-metic.onrender.com
- API publique: https://alodo-metic.onrender.com/api/beta
- Back-office: https://alodo-metic.vercel.app/admin

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
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alodo-metic.onrender.com
FRONTEND_URL=https://alodo-metic.vercel.app

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=12778
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=...
MYSQL_ATTR_SSL_CA=
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

Comptes seedes pour la beta:

```text
admin@alodo.mpme
demo@alodo.mpme
```

Les mots de passe initiaux sont definis dans le seeder admin du backend. Avant une production reelle, changer ces mots de passe et eviter de garder des identifiants par defaut dans les seeders.

## Branches

La branche de deploiement active est:

```text
main
```

Les anciennes branches `metric_backend` et `metric_frontend` ont servi au developpement separe. Le code deployable complet a ete fusionne dans `main`.
