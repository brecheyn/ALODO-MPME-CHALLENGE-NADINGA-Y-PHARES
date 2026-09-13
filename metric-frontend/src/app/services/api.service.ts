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
  providedIn: 'root'
})
export class ApiService {
  private readonly apiUrl = 'http://127.0.0.1:8000/api/beta';

  constructor(private http: HttpClient) {}

  getQuestions(): Observable<Question[]> {
    return this.http.get<{ data: Question[] }>(`${this.apiUrl}/questions`)
      .pipe(map(response => response.data));
  }

  getPublicStats(): Observable<number> {
    return this.http.get<{ data: { diagnostics_completed: number } }>(`${this.apiUrl}/stats/public`)
      .pipe(map(response => response.data.diagnostics_completed));
  }

  createDiagnostic(): Observable<string> {
    return this.http.post<{ data: { token: string } }>(`${this.apiUrl}/diagnostics`, {})
      .pipe(map(response => response.data.token));
  }

  submitAnswer(token: string, questionId: number, optionId: number): Observable<unknown> {
    return this.http.post(`${this.apiUrl}/diagnostics/${token}/answers`, {
      question_id: questionId,
      option_id: optionId,
    });
  }

  completeDiagnostic(token: string): Observable<unknown> {
    return this.http.post(`${this.apiUrl}/diagnostics/${token}/complete`, {});
  }

  getResult(token: string): Observable<DiagnosticResult> {
    return this.http.get<{ data: DiagnosticResult }>(`${this.apiUrl}/results/${token}`)
      .pipe(map(response => response.data));
  }

// ─── Back-office admin ───
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

  private auth() { return { Authorization: `Bearer ${this.adminToken()}` }; }
}
