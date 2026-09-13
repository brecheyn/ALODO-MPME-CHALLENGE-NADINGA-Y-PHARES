import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { Question } from '../models/questions.model';
import { DiagnosticResult } from '../models/result.model';

@Injectable({
  providedIn: 'root'   // singleton : UNE seule instance partagée par toute l'app
})
export class ApiService {
  // SEUL endroit de l'app où l'URL de l'API apparaît
  private readonly apiUrl = 'http://127.0.0.1:8000/api/beta';

  // Angular INJECTE le client HTTP grâce à provideHttpClient()
  constructor(private http: HttpClient) {}

  getQuestions(): Observable<Question[]> {
    return this.http.get<{ data: Question[] }>(`${this.apiUrl}/questions`)
      .pipe(
        map(response => response.data)   // on DÉBALLL l'enveloppe "data" → le composant reçoit le tableau pur
      );
  }
    getPublicStats(): Observable<number> {
    return this.http.get<{ data: { diagnostics_completed: number } }>(`${this.apiUrl}/stats/public`)
      .pipe(
        map(response => response.data.diagnostics_completed)  // on ne garde que le chiffre
      );
  }
   // "Commencer le diagnostic" → crée la session, renvoie le token
  createDiagnostic(): Observable<string> {
    return this.http.post<{ data: { token: string } }>(`${this.apiUrl}/diagnostics`, {})
      .pipe(map(response => response.data.token));
  }

  // Bouton "Suivant" → enregistre la réponse choisie
  submitAnswer(token: string, questionId: number, optionId: number): Observable<unknown> {
    return this.http.post(`${this.apiUrl}/diagnostics/${token}/answers`, {
      question_id: questionId,
      option_id: optionId,
    });
  }

  // Bouton "Voir mon résultat" → déclenche le moteur de scoring côté backend
  completeDiagnostic(token: string): Observable<unknown> {
    return this.http.post(`${this.apiUrl}/diagnostics/${token}/complete`, {});
  }

  // Écran de résultat : score, niveau, force/priorité + recommandations
  getResult(token: string): Observable<DiagnosticResult> {
    return this.http.get<{ data: DiagnosticResult }>(`${this.apiUrl}/results/${token}`)
      .pipe(map(response => response.data));
  }
}
