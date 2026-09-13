import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.html',
  imports: [RouterLink],
  styleUrl: './home.css',
})
export class Home implements OnInit {
  private api = inject(ApiService);

  // La preuve sociale (déplacée depuis App)
  diagnosticsCount = signal<number | null>(null);

  ngOnInit(): void {
    this.api.getPublicStats().subscribe({
      next: (count) => {
        this.diagnosticsCount.set(count);
        // Le compteur de preuve sociale arrive asynchronement → observer lancé après
        setTimeout(() => this.setupStatsObserver());
      },
      error: () => {
        this.diagnosticsCount.set(null);
        setTimeout(() => this.setupStatsObserver());
      },
    });
  }

  // ── LOT A : compteurs animés au scroll (IntersectionObserver) ──
  // Les éléments portant [data-count] animent leur texte de 0 → valeur
  // quand la rangée de stats entre dans le viewport.
  private statsAnimated = false;

  private animateCount(el: HTMLElement, target: number, suffix = ''): void {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      el.textContent = target + suffix;
      return;
    }
    const duration = 900;
    const start = performance.now();
    const tick = (now: number) => {
      const t = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - t, 3);
      el.textContent = Math.round(eased * target) + suffix;
      if (t < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  setupStatsObserver(): void {
    const observer = new IntersectionObserver((entries) => {
      if (entries.some(e => e.isIntersecting) && !this.statsAnimated) {
        this.statsAnimated = true;
        observer.disconnect();
        document.querySelectorAll<HTMLElement>('[data-count]').forEach(el => {
          this.animateCount(el, Number(el.dataset['count']), el.dataset['suffix'] ?? '');
        });
      }
    }, { threshold: 0.3 });
    const row = document.querySelector('.stats-row');
    if (row) observer.observe(row);
  }
}
