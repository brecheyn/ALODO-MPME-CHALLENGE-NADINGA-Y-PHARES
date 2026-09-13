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
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete(); // clé étrangère vers la table questions
            $table->string('text', 500);
            $table->unsignedTinyInteger('score');                   // points obtenus si l'utilisateur choisit cette option (0 à 3)
            $table->unsignedSmallInteger('display_order');          // ordre d'affichage des options
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
