import { Component, OnInit, inject, signal } from '@angular/core';
import { ApiService } from '../../services/api.service';

interface AdminStats {
  global: { diagnostics: number; avg_score: number; levels: Record<string, number> };
  dimensions: { code: string; label: string; avg_score: number; weak_pct: number }[];
  top_difficulties: { label: string; weak_pct: number }[];
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