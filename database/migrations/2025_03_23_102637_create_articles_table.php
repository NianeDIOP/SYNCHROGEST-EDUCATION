<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('designation');
            $table->text('description')->nullable();
            $table->string('unite_mesure');
            $table->decimal('quantite_stock', 10, 2)->default(0);
            $table->decimal('seuil_alerte', 10, 2)->default(0);
            $table->decimal('prix_unitaire', 10, 2)->default(0);
            $table->foreignId('categorie_id')->constrained('categories_articles');
            $table->string('emplacement')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};