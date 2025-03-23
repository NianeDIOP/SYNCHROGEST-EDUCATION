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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['recette', 'depense']);
            $table->foreignId('categorie_id')->constrained('categories_financieres');
            $table->decimal('montant', 10, 2);
            $table->date('date');
            $table->string('description');
            $table->string('reference')->nullable();
            $table->string('annee_scolaire');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};