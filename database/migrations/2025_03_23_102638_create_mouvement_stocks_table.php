<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained();
            $table->enum('type_mouvement', ['entrée', 'sortie']);
            $table->decimal('quantite', 10, 2);
            $table->date('date_mouvement');
            $table->string('motif')->nullable();
            $table->string('reference_document')->nullable();
            $table->foreignId('fournisseur_id')->nullable()->constrained();
            $table->string('destinataire')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mouvements_stock');
    }
};