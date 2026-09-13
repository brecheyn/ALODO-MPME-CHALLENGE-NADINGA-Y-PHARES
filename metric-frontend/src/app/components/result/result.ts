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

  // ── PARTAGE WHATSAPP ──
  // Texte adapté à la règle "sans humilier" : encourageant même avec un score bas.
  whatsappText(): string {
    const score = this.result()?.global_score ?? 0;
    const url = `${window.location.origin}/result/${this.token()}`;
    if (score < 50) {
      // Règle : "Chaque grande entreprise a commencé ici" — jamais de score mis en avant
      return 'Je viens de faire le diagnostic ALODO Metric pour structurer ma MPME.'
        + '\nMon premier levier de progrès est identifié — chaque grande entreprise a commencé ici !'
        + `\nFais le tien aussi : ${url}`;
    }
    return 'Je viens de faire le diagnostic ALODO Metric pour structurer ma MPME.'
      + `\nMon score : ${score}/100`
      + `\nFais le tien aussi : ${url}`;
  }

  shareWhatsapp(): void {
    window.open(`https://wa.me/?text=${encodeURIComponent(this.whatsappText())}`, '_blank');
  }

  // ── EXPORT PDF ──
  // Impression du résultat : le navigateur génère le PDF (Ctrl+P → "Enregistrer en PDF").
  // On ouvre une fenêtre d'impression contenant le résultat mis en page.
  exportPdf(): void {
    const score = this.result()?.global_score ?? 0;
    const low = score < 50;   // règle "sans humilier" : mise en page encourageante
    const url = `${window.location.origin}/result/${this.token()}`;

    const recos = document.querySelectorAll<HTMLElement>('.reco-item');
    const recosHtml = Array.from(recos).map(r =>
      `<li style="margin-bottom:8px">${r.textContent?.trim()}</li>`).join('');

    const w = window.open('', '_blank', 'width=800,height=900');
    if (!w) return;
    w.document.write(`
      <html>
        <head>
          <title>Résultat ALODO Metric</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 40px; color: #1A1A1A; }
            .score-box { text-align: center; margin-bottom: 32px; }
            .score-value { font-size: 64px; font-weight: 800; color: ${low ? '#E08A2E' : '#2E8A4E'}; }
            .score-label { color: #6A6A6A; margin-top: 8px; }
            h2 { font-size: 18px; border-bottom: 2px solid #E5E5E5; padding-bottom: 8px; }
            .encourage { background: #FDF4E7; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
          </style>
        </head>
        <body>
          <div class="score-box">
            <div class="score-value">${score}/100</div>
            <div class="score-label">
              ${low
                ? 'Votre premier levier de progrès est identifié — chaque grande entreprise a commencé ici.'
                : 'Score de structuration — continuez sur cette lancée !'}
            </div>
          </div>
          ${low ? `<div class="encourage"><strong>Commencez par cette action :</strong><ul>${recosHtml}</ul></div>` : ''}
          <h2>Vos recommandations</h2>
          <ul>${recosHtml}</ul>
          <p style="color:#9A9A9A; font-size:12px; margin-top:32px">
            Généré par ALODO Metric — ${new Date().toLocaleDateString('fr-FR')}<br>
            Refaire le diagnostic : ${url}
          </p>
        </body>
      </html>
    `);
    w.document.close();
    setTimeout(() => w.print(), 300);   // laisse le rendu se charger avant l'impression
  }
}