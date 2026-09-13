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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_id')->constrained('dimensions')->cascadeOnDelete(); // la dimension concernée
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();         // le niveau de maturité (même dimension, texte différent selon le niveau)
            $table->string('title', 200);   // titre court de la recommandation
            $table->text('action_text');    // l'action concrète et actionnable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
