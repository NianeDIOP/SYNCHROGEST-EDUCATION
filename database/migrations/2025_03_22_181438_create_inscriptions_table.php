<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained();
            $table->foreignId('classe_id')->constrained();
            $table->string('annee_scolaire');
            $table->date('date_inscription');
            $table->decimal('montant_paye', 10, 2);
            $table->decimal('montant_restant', 10, 2);
            $table->string('numero_recu')->unique();
            $table->enum('statut_paiement', ['Complet', 'Partiel', 'Non payé'])->default('Non payé');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscriptions');
    }
};