# ALODO Metric Frontend

Application Angular du diagnostic ALODO Metric. Elle affiche la page publique, le questionnaire, la page de resultat et le back-office admin.

## Stack

- Angular 22
- Angular Router
- HttpClient
- Deploiement Vercel

## Installation Locale

```bash
npm install
npm run start
```

URL locale:

```text
http://localhost:4200
```

Le frontend local appelle:

```text
http://127.0.0.1:8000/api/beta
```

## Build

```bash
npm run build
```

Dossier genere:

```text
dist/metric-frontend/browser
```

## Deploiement Vercel

Parametres Vercel:

```text
Root Directory: metric-frontend
Build Command: npm run build
Output Directory: dist/metric-frontend/browser
```

Le fichier `vercel.json` gere le fallback SPA:

```json
{
  "rewrites": [
    { "source": "/(.*)", "destination": "/index.html" }
  ]
}
```

## Environnements

Production:

```text
src/environments/environment.ts
```

URL API:

```ts
apiUrl: 'https://alodo-metic.onrender.com/api/beta'
```

Developpement:

```text
src/environments/environment.development.ts
```

URL API:

```ts
apiUrl: 'http://127.0.0.1:8000/api/beta'
```

## Routes

```text
/              Accueil
/quiz          Questionnaire
/result/:token Resultat d'un diagnostic
/admin         Back-office
```

## Back-office Admin

URL:

```text
https://alodo-metic.vercel.app/admin
```

Comptes beta seedes par le backend:

```text
admin@alodo.mpme
demo@alodo.mpme
```

Les mots de passe initiaux sont definis cote backend dans le seeder admin et doivent etre changes avant une production reelle.

Fonctionnalites:

- connexion admin par token Sanctum
- statistiques globales
- statistiques par dimension
- lecture et edition des questions
- edition des scores d'options
- suppression protegee des questions non utilisees

Les tokens admin sont stockes dans `sessionStorage`.

## Service API

Le point central des appels HTTP est:

```text
src/app/services/api.service.ts
```

Principales methodes:

```text
getQuestions()
getPublicStats()
createDiagnostic()
submitAnswer()
completeDiagnostic()
getResult()
adminLogin()
adminLogout()
adminStats()
adminQuestions()
saveQuestion()
deleteQuestion()
saveOption()
```
