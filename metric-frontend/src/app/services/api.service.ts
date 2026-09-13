import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { Question } from '../models/questions.model';
import { DiagnosticResult } from '../models/result.model';

export interface AdminStats {
  global: { diagnostics: number; avg_score: number; levels: Record<string, number> };
  dimensions: { code: string; label: string; avg_score: number; weak_pct: number }[];
  top_difficulties: { label: string; weak_pct: number }[];
}

@Injectable({
  providedIn: 'root'   // une seule instance partagée dans l'application
})
export class ApiService {
  // SEUL endroit de l'app où l'URL de l'API apparaît
  private readonly apiUrl = 'http://127.0.0.1:8000/api/beta';

  // Angular INJECTE le client HTTP grâce à provideHttpClient()
  constructor(private http: HttpClient) {}

  getQuestions(): Observable<Question[]> {
    return this.http.get<{ data: Question[] }>(`${this.apiUrl}/questions`)
      .pipe(
        map(response => response.data)   // extraction du tableau depuis l'enveloppe "data"
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

  // ─── BACK-OFFICE ADMIN ───
  // Le token admin vit dans sessionStorage : survit au refresh, meurt à la fermeture
  adminToken(): string | null { return sessionStorage.getItem('admin_token'); }

  adminLogin(email: string, password: string): Observable<{ name: string }> {
    return this.http.post<{ data: { token: string; admin: { name: string } } }>(
      `${this.apiUrl}/admin/login`, { email, password }
    ).pipe(map(r => {
      sessionStorage.setItem('admin_token', r.data.token);
      return r.data.admin;
    }));
  }

  adminLogout(): void {
    const token = this.adminToken();
    if (token) {
      this.http.post(`${this.apiUrl}/admin/logout`, {}, { headers: { Authorization: `Bearer ${token}` } })
        .subscribe({ complete: () => sessionStorage.removeItem('admin_token') });
    }
    sessionStorage.removeItem('admin_token');
  }

  adminStats(): Observable<AdminStats> {
    return this.http.get<{ data: AdminStats }>(`${this.apiUrl}/admin/stats`, {
      headers: { Authorization: `Bearer ${this.adminToken()}` },
    }).pipe(map(r => r.data));
  }

  // ── CRUD questions (phase 4) ──
  private auth() { return { Authorization: `Bearer ${this.adminToken()}` }; }

  adminQuestions(): Observable<unknown> {
    return this.http.get<{ data: unknown }>(`${this.apiUrl}/admin/questions`, { headers: this.auth() })
      .pipe(map(r => r.data));
  }

  saveQuestion(id: number, changes: Record<string, unknown>): Observable<unknown> {
    return this.http.put(`${this.apiUrl}/admin/questions/${id}`, changes, { headers: this.auth() });
  }

  deleteQuestion(id: number): Observable<unknown> {
    return this.http.delete(`${this.apiUrl}/admin/questions/${id}`, { headers: this.auth() });
  }

  saveOption(id: number, changes: Record<string, unknown>): Observable<unknown> {
    return this.http.put(`${this.apiUrl}/admin/options/${id}`, changes, { headers: this.auth() });
  }
}
