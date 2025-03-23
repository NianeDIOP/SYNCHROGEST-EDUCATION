<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('personne_contact')->nullable();
            $table->string('telephone_contact')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fournisseurs');
    }
};