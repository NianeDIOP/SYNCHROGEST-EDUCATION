<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('ine')->unique();
            $table->string('prenom');
            $table->string('nom');
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance');
            $table->string('lieu_naissance');
            $table->boolean('existence_extrait')->default(false);
            $table->foreignId('classe_id')->constrained();
            $table->string('motif_entre')->nullable();
            $table->enum('statut', ['Nouveau', 'Ancien', 'Redoublant'])->default('Nouveau');
            $table->string('contact_parent')->nullable();
            $table->string('adresse')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('eleves');
    }
};