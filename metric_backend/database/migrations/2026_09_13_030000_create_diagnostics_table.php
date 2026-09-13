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
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();          // identifiant de session anonyme (lien partageable)
            $table->string('status', 20)->default('in_progress'); // in_progress | completed
            $table->string('email')->nullable();            // email FACULTATIF (jamais obligatoire - friction minimale)
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete(); // rempli au moment du résultat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostics');
    }
};
