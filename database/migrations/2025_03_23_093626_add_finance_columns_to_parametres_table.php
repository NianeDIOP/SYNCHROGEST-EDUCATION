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
        Schema::table('parametres', function (Blueprint $table) {
            $table->string('devise')->default('FCFA')->nullable();
            $table->integer('annee_fiscale_debut')->default(1)->nullable();
            $table->boolean('paiement_echelonne')->default(false);
            $table->integer('nb_echeances')->default(3)->nullable();
            $table->boolean('frais_retard')->default(false);
            $table->decimal('taux_retard', 5, 2)->default(5.00)->nullable();
            $table->boolean('rappels_auto')->default(false);
            $table->integer('delai_rappel')->default(7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametres', function (Blueprint $table) {
            $table->dropColumn([
                'devise',
                'annee_fiscale_debut',
                'paiement_echelonne',
                'nb_echeances',
                'frais_retard',
                'taux_retard',
                'rappels_auto',
                'delai_rappel',
            ]);
        });
    }
};