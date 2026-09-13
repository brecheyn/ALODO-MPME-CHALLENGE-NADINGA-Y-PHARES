import { Component } from '@angular/core';
import { RouterOutlet, RouterLink, Router } from '@angular/router';

@Component({
  selector: 'app-root',
  // RouterOutlet : la "prise" où le routeur branche la page selon l'URL
  // RouterLink   : pour les liens internes de la navbar (navigation SANS rechargement)
  imports: [RouterOutlet, RouterLink],
  templateUrl: './app.html',
  styleUrl: './app.css',
})
export class App {
  constructor(private router: Router) {}

  // Défilement vers "Comment ça marche" SANS modifier l'URL (pas de #fragment).
  // Si on n'est pas sur l'accueil (la section n'existe pas encore), on y navigue d'abord.
  scrollToHow(event: Event): void {
    event.preventDefault();   // empêche le comportement du href="#"
    const section = document.getElementById('how-it-works');
    if (section) {
      section.scrollIntoView({ behavior: 'smooth' });
    } else {
      this.router.navigate(['/']).then(() =>
        document.getElementById('how-it-works')?.scrollIntoView({ behavior: 'smooth' })
      );
    }
  }
}
