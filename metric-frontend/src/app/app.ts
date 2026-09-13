import { Component } from '@angular/core';
import { RouterOutlet, RouterLink } from '@angular/router';

@Component({
  selector: 'app-root',
  // RouterOutlet : la "prise" où le routeur branche la page selon l'URL
  // RouterLink   : pour les liens internes de la navbar (navigation SANS rechargement)
  imports: [RouterOutlet, RouterLink],
  templateUrl: './app.html',
  styleUrl: './app.css',
})
export class App {
  // Le shell ne porte AUCUNE donnée : il structure (navbar / contenu / footer).
  // Les données vivent dans les composants de page (Home, Quiz...).
}
