import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { DiagnosticResult, DimensionScore } from '../../models/result.model';

@Component({
  selector: 'app-result',
  imports: [RouterLink],
  templateUrl: './result.html',
  styleUrl: './result.css',
})
export class Result implements OnInit {
  private api = inject(ApiService);
  private route = inject(ActivatedRoute);

  token    = signal('');
  result   = signal<DiagnosticResult | null>(null);
  error    = signal<string | null>(null);
  copied   = signal(false);   // feedback "lien copié" du bouton Partager

  // La jauge circulaire : conic-gradient orange proportionnel au score
  ringGradient = computed(() => {
    const score = this.result()?.global_score ?? 0;
    return `conic-gradient(var(--alodo-orange) ${score * 3.6}deg, #E9E9E6 0deg)`;
  });

  // Record (objet indexé) → tableau itérable pour @for
  dimensionEntries = computed(() => {
    const details: Record<string, DimensionScore> = this.result()?.details ?? {};
    return Object.entries(details).map(([code, d]) => ({ code, ...d }));
  });

  ngOnInit(): void {
    // :token lu depuis l'URL → le résultat est partageable/rafraîchissable
    this.token.set(this.route.snapshot.paramMap.get('token') ?? '');
    this.api.getResult(this.token()).subscribe({
      next: (data) => this.result.set(data),
      error: () => this.error.set("Aucun résultat pour ce lien. Terminez d'abord un diagnostic."),
    });
  }

  share(): void {
    const url = `${window.location.origin}/result/${this.token()}`;
    navigator.clipboard.writeText(url).then(() => {
      this.copied.set(true);
      setTimeout(() => this.copied.set(false), 2000);
    });
  }
}