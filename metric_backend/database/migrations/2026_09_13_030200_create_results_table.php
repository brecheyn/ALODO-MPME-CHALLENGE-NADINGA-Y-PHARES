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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostic_id')->constrained('diagnostics')->cascadeOnDelete(); // 1 résultat = 1 diagnostic terminé
            $table->unsignedTinyInteger('global_score');      // score global 0-100
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete(); // niveau de maturité ("Niveau 3 · En structuration")
            $table->string('strength_text', 200)->nullable(); // le point fort mis en avant ("À préserver")
            $table->string('priority_text', 200)->nullable(); // la priorité ("À structurer")
            $table->json('details')->nullable();              // scores par dimension : {"tenue_comptes": 86, "tresorerie": 48}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
