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
      next: (count) => this.diagnosticsCount.set(count),
      error: () => this.diagnosticsCount.set(null),
    });
  }
}
