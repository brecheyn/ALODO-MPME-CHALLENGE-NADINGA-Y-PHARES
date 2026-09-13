import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { Router } from '@angular/router';
import { Question } from '../../models/questions.model';

@Component({
  selector: 'app-quiz',
  templateUrl: './quiz.html',
  styleUrl: './quiz.css',
})
export class Quiz implements OnInit {
  private api = inject(ApiService);
  private router = inject(Router);

  // ── L'état (signals) : TOUT ce que la vue lit ──
  questions        = signal<Question[]>([]);
  currentIndex     = signal(0);
  selectedOptionId = signal<number | null>(null);  // null = rien sélectionné (bouton désactivé)
  token            = signal<string | null>(null);
  error            = signal<string | null>(null);

  // ── Les valeurs DÉRIVÉES (computed) : recalculées automatiquement ──
  currentQuestion = computed(() => this.questions()[this.currentIndex()] ?? null);
  isLast          = computed(() => this.currentIndex() === this.questions().length - 1);

  ngOnInit(): void {
    // Charge le questionnaire ET crée la session en parallèle
    this.api.getQuestions().subscribe({
      next: (data) => this.questions.set(data),
      error: (err) => this.error.set(err.message),
    });
    this.api.createDiagnostic().subscribe({
      next: (token) => this.token.set(token),
      error: () => this.error.set('Impossible de démarrer le diagnostic.'),
    });
  }

  select(optionId: number): void {
    this.selectedOptionId.set(optionId);   // un clic = une sélection (écrase l'ancienne)
  }

  next(): void {
    const question = this.currentQuestion();
    const optionId = this.selectedOptionId();
    const token = this.token();
    if (!question || !optionId || !token) return;   // garde-fou (bouton déjà désactivé, on double)

    this.api.submitAnswer(token, question.id, optionId).subscribe({
      next: () => {
        if (this.isLast()) {
          // Dernière réponse → on déclenche le scoring, puis on navigue vers /result/{token}
          this.api.completeDiagnostic(token).subscribe({
            next: () => this.router.navigate(['/result', token]),
            error: () => this.error.set('Le calcul du résultat a échoué.'),
          });
        } else {
          this.currentIndex.update((i) => i + 1);   // question suivante
          this.selectedOptionId.set(null);           // reset : bouton redevient grisé
        }
      },
      error: () => this.error.set("Impossible d'enregistrer la réponse."),
    });
  }
}
