import { Component, OnInit, inject, signal } from '@angular/core';
import { ApiService } from './services/api.service';
import { Question } from './models/questions.model';

@Component({
  selector: 'app-root',
  imports: [],
  templateUrl: './app.html',
  styleUrl: './app.css',
})
export class App implements OnInit {
  // inject() : Angular me donne une instance du service SANS constructor
  private api = inject(ApiService);

  // signal : une "case mémoire réactive" — quand elle change, la vue se met à jour TOUTE SEULE
  questions = signal<Question[]>([]);
  error     = signal<string | null>(null);

  ngOnInit(): void {
    // ngOnInit : cycle de vie — appelé quand le composant apparaît
    this.api.getQuestions().subscribe({
      next: (data) => this.questions.set(data),   // succès → on remplit le signal
      error: (err) => this.error.set(err.message), // échec → on stocke l'erreur
    });
  }
}
