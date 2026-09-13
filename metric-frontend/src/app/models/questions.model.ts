
export interface QuestionOption {
  id: number;            // correspond EXACTEMENT aux colonnes de ta migration
  text: string;
  score: number;
  display_order: number;
}

export interface Question {
  id: number;
  dimension_id: number;
  text: string;
  display_order: number;
  options: QuestionOption[];   // la relation hasMany() du backend !
}
