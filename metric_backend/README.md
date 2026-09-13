# ALODO Metric Backend

API Laravel du diagnostic ALODO Metric. Elle gere les questions, les diagnostics, les reponses, le scoring, les resultats, les statistiques publiques et le back-office admin.

## Stack

- Laravel 13
- PHP 8.4 en production Docker Render
- MySQL Aiven
- Laravel Sanctum pour les tokens admin

## Installation Locale

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

API locale:

```text
http://localhost:8000/api/beta
```

## Configuration `.env`

Exemple local MySQL:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:4200

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=metric
DB_USERNAME=root
DB_PASSWORD=
MYSQL_ATTR_SSL_CA=
```

Exemple Render + Aiven:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alodo-metic.onrender.com
FRONTEND_URL=https://alodo-metic.vercel.app

DB_CONNECTION=mysql
DB_HOST=your-aiven-host
DB_PORT=12778
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=your-aiven-password
MYSQL_ATTR_SSL_CA=
```

Ne jamais commit le fichier `.env`.

## Endpoints Publics

Base URL:

```text
/api/beta
```

Routes:

```text
GET  /questions
GET  /dimensions
POST /diagnostics
POST /diagnostics/{token}/answers
POST /diagnostics/{token}/complete
GET  /results/{token}
GET  /stats/public
```

Parcours:

1. Le frontend appelle `GET /questions`.
2. Il cree une session avec `POST /diagnostics`.
3. Chaque reponse est envoyee avec `POST /diagnostics/{token}/answers`.
4. La fin du questionnaire appelle `POST /diagnostics/{token}/complete`.
5. Le resultat est recupere avec `GET /results/{token}`.

## Administration

Routes admin:

```text
POST   /api/beta/admin/login
GET    /api/beta/admin/me
POST   /api/beta/admin/logout
GET    /api/beta/admin/stats
GET    /api/beta/admin/questions
POST   /api/beta/admin/questions
PUT    /api/beta/admin/questions/{question}
DELETE /api/beta/admin/questions/{question}
POST   /api/beta/admin/options
PUT    /api/beta/admin/options/{option}
```

Les routes sauf `login` demandent:

```http
Authorization: Bearer {token}
```

Login:

```bash
curl -X POST https://alodo-metic.onrender.com/api/beta/admin/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"admin@alodo.mpme\",\"password\":\"your-admin-password\"}"
```

Comptes seedes pour la beta:

```text
admin@alodo.mpme
demo@alodo.mpme
```

Important: ces comptes sont des comptes beta/demo. Les mots de passe initiaux sont definis dans le seeder admin. Changer les mots de passe avant une utilisation en production reelle.

## Seeders

Les seeders creent:

- 4 niveaux de maturite
- 8 dimensions
- 8 questions
- 32 options
- recommandations par dimension et niveau
- comptes admin beta

Commande:

```bash
php artisan db:seed
```

Les seeders utilisent `updateOrCreate`, donc ils peuvent etre relances sans dupliquer les donnees principales.

## Deploiement Render

Le deploiement passe par le `Dockerfile` a la racine du repo et le `render.yaml` racine.

Le conteneur:

- utilise PHP 8.4 Apache
- installe les extensions PHP necessaires
- lance `composer install --no-dev`
- pointe Apache vers `public/`
- lance `php artisan migrate --force`
- lance `php artisan db:seed --force`

Verifier apres deploiement:

```bash
curl https://alodo-metic.onrender.com/api/beta/stats/public
curl https://alodo-metic.onrender.com/api/beta/questions
```

## CORS

Les origines autorisees sont dans `config/cors.php`:

```text
env('FRONTEND_URL')
https://alodo-metic.vercel.app
http://localhost:4200
http://127.0.0.1:4200
```

En production Render, definir:

```env
FRONTEND_URL=https://alodo-metic.vercel.app
```
