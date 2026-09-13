<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_id')->constrained('diagnostics')->cascadeOnDelete(); // la session de diagnostic
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();     // la question répondue
            $table->foreignId('option_id')->constrained('question_options')->cascadeOnDelete(); // l'option choisie (porte le score)
            $table->unique(['diagnostic_id', 'question_id']); // UNE seule réponse par question et par diagnostic
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
