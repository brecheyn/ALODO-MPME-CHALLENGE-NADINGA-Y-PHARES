import { Component, OnInit, inject, signal } from '@angular/core';
import { ApiService } from '../../services/api.service';

interface AdminStats {
  global: { diagnostics: number; avg_score: number; levels: Record<string, number> };
  dimensions: { code: string; label: string; avg_score: number; weak_pct: number }[];
  top_difficulties: { label: string; weak_pct: number }[];
}

// Ce que renvoie GET /admin/questions (question + ses options)
interface AdminQuestion {
  id: number;
  text: string;
  display_order: number;
  dimension_id: number;
  options: AdminOption[];
}
interface AdminOption {
  id: number;
  text: string;
  score: number;
  display_order: number;
}

@Component({
  selector: 'app-admin',
  templateUrl: './admin.html',
  styleUrl: './admin.css',
})
export class Admin implements OnInit {
  private api = inject(ApiService);

  loggedIn = signal(false);
  adminName = signal('');
  email = signal('');
  password = signal('');
  loginError = signal<string | null>(null);
  loading = signal(false);

  stats = signal<AdminStats | null>(null);

  // ── Onglet "Questions" (phase 4) ──
  tab = signal<'stats' | 'questions'>('stats');
  questions = signal<AdminQuestion[]>([]);

  loadQuestions(): void {
    this.api.adminQuestions().subscribe({
      next: (data) => this.questions.set(data as AdminQuestion[]),
      error: () => this.loginError.set('Chargement des questions impossible.'),
    });
  }

  setTab(tab: 'stats' | 'questions'): void {
    this.tab.set(tab);
    if (tab === 'questions') this.loadQuestions();
  }

  saveQuestionText(q: AdminQuestion): void {
    this.api.saveQuestion(q.id, { text: q.text, display_order: q.display_order }).subscribe({
      next: () => this.loadQuestions(),
      error: (err) => alert(this.errorMessage(err)),
    });
  }

  removeQuestion(q: AdminQuestion): void {
    if (!confirm(`Supprimer la question "${q.text.slice(0, 40)}..." ?`)) return;
    this.api.deleteQuestion(q.id).subscribe({
      next: () => this.loadQuestions(),
      error: (err) => alert(this.errorMessage(err)),   // ex: 409 réponses existantes
    });
  }

  saveOptionScore(opt: AdminOption): void {
    this.api.saveOption(opt.id, { score: opt.score }).subscribe({
      next: () => this.loadQuestions(),
      error: (err) => { alert(this.errorMessage(err)); this.loadQuestions(); },  // 409 = score verrouillé
    });
  }

  private errorMessage(err: unknown): string {
    const e = err as { error?: { message?: string } };
    return e?.error?.message ?? 'Erreur inconnue.';
  }

  ngOnInit(): void {
    // Token présent en session ? → dashboard direct, sinon écran de login
    if (this.api.adminToken()) {
      this.loadStats();
    }
  }

  login(): void {
    this.loading.set(true);
    this.loginError.set(null);
    this.api.adminLogin(this.email(), this.password()).subscribe({
      next: (admin) => {
        this.adminName.set(admin.name);
        this.loggedIn.set(true);
        this.loadStats();
      },
      error: () => {
        this.loginError.set('Identifiants incorrects.');
        this.loading.set(false);
      },
    });
  }

  loadStats(): void {
    this.api.adminStats().subscribe({
      next: (s) => { this.stats.set(s); this.loggedIn.set(true); this.loading.set(false); },
      error: () => {
        // token expiré/invalide → retour au login
        this.api.adminLogout();
        this.loggedIn.set(false);
        this.loading.set(false);
      },
    });
  }

  logout(): void {
    this.api.adminLogout();
    this.loggedIn.set(false);
    this.stats.set(null);
  }
}