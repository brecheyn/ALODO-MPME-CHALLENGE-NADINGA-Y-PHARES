
import { ApplicationConfig, provideZonelessChangeDetection } from '@angular/core';
import { provideRouter, withInMemoryScrolling } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { routes } from './app.routes';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZonelessChangeDetection(),
    // scrollPositionRestoration: 'top' → chaque navigation arrive en HAUT de page
    // (sans ça, on arrive au milieu du quiz si on avait scrollé l'accueil)
    provideRouter(routes, withInMemoryScrolling({
      scrollPositionRestoration: 'top',
      anchorScrolling: 'enabled',   // défilement fluide vers les ancres (#how-it-works)
    })),
    provideHttpClient(),
  ],
};

