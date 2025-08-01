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
        Schema::create('paiement_paydunyas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membre_id');
            $table->string('reference')->unique(); // Référence interne
            $table->string('invoice_token')->unique(); // Token PayDunya
            $table->string('invoice_url'); // URL PayDunya
            $table->decimal('montant', 10, 2);
            $table->string('telephone');
            $table->string('operateur'); // Orange Money, Free Money, etc.
            $table->string('mode_paiement'); // orange-money-senegal, etc.
            $table->string('type_cotisation'); // mensuelle, annuelle, etc.
            $table->text('description')->nullable();
            $table->enum('statut', ['en_cours', 'reussi', 'echoue', 'annule'])->default('en_cours');
            $table->string('statut_paydunya')->nullable(); // pending, completed, failed, cancelled
            $table->timestamp('date_paiement')->nullable();
            $table->json('donnees_paydunya')->nullable(); // Données complètes de PayDunya
            $table->timestamps();
            
            // Clés étrangères
            $table->foreign('membre_id')->references('id')->on('membres')->onDelete('cascade');
            
            // Index pour performance
            $table->index(['membre_id', 'statut']);
            $table->index(['invoice_token']);
            $table->index(['reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_paydunyas');
    }
};
