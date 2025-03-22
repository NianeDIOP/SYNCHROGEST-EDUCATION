<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->decimal('frais_inscription', 10, 2);
            $table->decimal('frais_scolarite', 10, 2);
            $table->boolean('est_niveau_examen')->default(false);
            $table->decimal('frais_examen', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveaux');
    }
};