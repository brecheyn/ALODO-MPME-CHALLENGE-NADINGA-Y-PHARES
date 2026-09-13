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
        Schema::create('dimensions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();            // ex: "liquidity", "governance"
            $table->string('label', 150);                    // ex: "Liquidité"
            $table->text('description')->nullable();         // texte explicatif (optionnel)
            $table->unsignedSmallInteger('display_order');   // ordre d'affichage (1 à 8)
            $table->decimal('weight', 5, 2)->default(1.00);  // pondération dans le score global
            $table->timestamps();                            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimensions');
    }
};
