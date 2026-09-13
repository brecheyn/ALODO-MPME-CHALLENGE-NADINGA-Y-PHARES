import { Routes } from '@angular/router';
import { Home } from './components/home/home';
import { Quiz } from './components/quiz/quiz';
import { Result } from './components/result/result';

export const routes: Routes = [
  { path: '', component: Home },      // URL "/"      → l'accueil (hero + stats)
  { path: 'quiz', component: Quiz },  // URL "/quiz"  → le questionnaire
  // :token = paramètre d'URL → chaque résultat est accessible par un lien partageable
  { path: 'result/:token', component: Result },
];
