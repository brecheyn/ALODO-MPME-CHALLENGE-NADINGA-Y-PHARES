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

  // ── Animation de la jauge (lot B) ──
  displayedScore = signal(0);                       // le chiffre qui compte 0 → score
  ringGradient   = signal('conic-gradient(var(--alodo-orange) 0deg, #E9E9E6 0deg)'); // départ : 0°

  // ── Règle "sans humilier" : score bas = encouragement + action en premier ──
  isLow = computed(() => (this.result()?.global_score ?? 0) < 50);

  // Barres en cascade : 1 = animée jusqu'à sa largeur, 0 = encore à zéro
  barsShown = signal(0);

  private animateRing(target: number): void {
    // Accessibilité : pas d'animation si l'utilisateur refuse le mouvement
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.displayedScore.set(target);
      this.ringGradient.set(`conic-gradient(var(--alodo-orange) ${target * 3.6}deg, #E9E9E6 0deg)`);
      this.barsShown.set(this.dimensionEntries().length);
      return;
    }

    const duration = 900;   // ms
    const start = performance.now();

    const tick = (now: number) => {
      const t = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - t, 3);            // ease-out cubic
      const value = Math.round(eased * target);
      this.displayedScore.set(value);
      this.ringGradient.set(`conic-gradient(var(--alodo-orange) ${value * 3.6}deg, #E9E9E6 0deg)`);
      if (t < 1) requestAnimationFrame(tick);
      else this.revealBars();                          // la jauge finie → les barres
    };
    requestAnimationFrame(tick);
  }

  private revealBars(): void {
    const total = this.dimensionEntries().length;
    for (let i = 1; i <= total; i++) {
      setTimeout(() => this.barsShown.set(i), i * 120);  // cascade 120ms par barre
    }
  }

  // Record (objet indexé) → tableau itérable pour @for
  dimensionEntries = computed(() => {
    const details: Record<string, DimensionScore> = this.result()?.details ?? {};
    return Object.entries(details).map(([code, d]) => ({ code, ...d }));
  });

  ngOnInit(): void {
    // :token lu depuis l'URL → le résultat est partageable/rafraîchissable
    this.token.set(this.route.snapshot.paramMap.get('token') ?? '');
    this.api.getResult(this.token()).subscribe({
      next: (data) => {
        this.result.set(data);
        this.animateRing(data.global_score);   // compte 0 → score, puis cascade des barres
      },
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