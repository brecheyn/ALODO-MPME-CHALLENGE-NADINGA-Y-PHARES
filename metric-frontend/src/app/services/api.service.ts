import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { Question } from '../models/questions.model';

@Injectable({
  providedIn: 'root'   // singleton : UNE seule instance partagée par toute l'app
})
export class ApiService {
  // SEUL endroit de l'app où l'URL de l'API apparaît
  private readonly apiUrl = 'http://127.0.0.1:8000/api/beta';

  // Angular INJECTE le client HTTP grâce à provideHttpClient()
  constructor(private http: HttpClient) {}

  getQuestions(): Observable<Question[]> {
    return this.http.get<{ data: Question[] }>(`${this.apiUrl}/questions`)
      .pipe(
        map(response => response.data)   // on DÉBALLL l'enveloppe "data" → le composant reçoit le tableau pur
      );
  }
}
