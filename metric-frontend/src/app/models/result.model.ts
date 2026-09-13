// Contrat de l'API résultat

export interface ResultLevel {
  number: number;
  name?: string;          // présent sur GET /results ("Niveau 3")
  label: string;          // "En structuration"
  encouragement: string;
}

export interface DimensionScore {
  label: string;          // "Trésorerie"
  score: number;          // 0 à 100
}

export interface Recommendation {
  title: string;          // "Suivre une trésorerie hebdomadaire"
  action_text: string;    // l'action concrète à mener
}

export interface DiagnosticResult {
  global_score: number;
  level: ResultLevel;
  strength: { label: string };  // le point fort "À préserver"
  priority: { label: string };  // la priorité "À structurer"
  // details = les scores par dimension, indexés par code :
  // { "tresorerie": { label: "Trésorerie", score: 48 }, ... }
  details: Record<string, DimensionScore>;
  recommendations: Recommendation[];
}
