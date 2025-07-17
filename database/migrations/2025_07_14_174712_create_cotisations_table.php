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
    Schema::create('cotisations', function (Blueprint $table) {
        $table->id();
        $table->string('type'); // exemple : "mensuelle", "exceptionnelle"
        $table->decimal('montant', 10, 2);
        $table->date('date_paiement');
        $table->unsignedBigInteger('membre_id');
        $table->foreign('membre_id')->references('id')->on('membres')->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};
