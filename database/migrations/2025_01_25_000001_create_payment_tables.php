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
        // Créer la table paiements si elle n'existe pas
        if (!Schema::hasTable('paiements')) {
            Schema::create('paiements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('membre_id')->nullable();
                $table->unsignedBigInteger('dahira_id')->nullable();
                $table->decimal('montant', 10, 2)->nullable();
                $table->string('telephone')->nullable();
                $table->enum('operateur', ['orange', 'free', 'wave'])->nullable();
                $table->enum('type_cotisation', ['mensuelle', 'annuelle', 'evenement', 'zakat'])->nullable();
                $table->text('description')->nullable();
                $table->enum('statut', ['en_attente', 'reussi', 'echoue', 'annule'])->default('en_attente');
                $table->string('method_paiement')->default('mobile_money');
                $table->string('transaction_id')->nullable();
                $table->string('reference_transaction')->nullable();
                $table->timestamp('date_paiement')->nullable();
                $table->timestamps();

                // Index pour améliorer les performances
                $table->index(['membre_id', 'dahira_id']);
                $table->index(['statut']);
                $table->index(['operateur']);
                $table->index(['reference_transaction']);

                // Clés étrangères
                $table->foreign('membre_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('dahira_id')->references('id')->on('dahiras')->onDelete('set null');
            });
        }

        // Améliorer la table cotisations si elle existe
        if (Schema::hasTable('cotisations')) {
            Schema::table('cotisations', function (Blueprint $table) {
                if (!Schema::hasColumn('cotisations', 'paiement_id')) {
                    $table->unsignedBigInteger('paiement_id')->nullable()->after('montant');
                    $table->foreign('paiement_id')->references('id')->on('paiements')->onDelete('set null');
                }
                
                if (!Schema::hasColumn('cotisations', 'statut')) {
                    $table->enum('statut', ['en_attente', 'payee', 'en_retard', 'annulee'])->default('en_attente')->after('paiement_id');
                }
                
                if (!Schema::hasColumn('cotisations', 'periode')) {
                    $table->string('periode')->nullable()->after('statut');
                }
            });
        } else {
            // Créer la table cotisations si elle n'existe pas
            Schema::create('cotisations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('membre_id');
                $table->unsignedBigInteger('dahira_id');
                $table->decimal('montant', 10, 2);
                $table->unsignedBigInteger('paiement_id')->nullable();
                $table->enum('statut', ['en_attente', 'payee', 'en_retard', 'annulee'])->default('en_attente');
                $table->string('periode')->nullable();
                $table->enum('type', ['mensuelle', 'annuelle', 'evenement', 'zakat'])->default('mensuelle');
                $table->text('description')->nullable();
                $table->date('date_echeance');
                $table->timestamps();

                // Index
                $table->index(['membre_id', 'dahira_id']);
                $table->index(['statut']);
                $table->index(['type']);
                $table->index(['date_echeance']);

                // Clés étrangères
                $table->foreign('membre_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('dahira_id')->references('id')->on('dahiras')->onDelete('cascade');
                $table->foreign('paiement_id')->references('id')->on('paiements')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les clés étrangères et colonnes ajoutées à cotisations
        if (Schema::hasTable('cotisations')) {
            Schema::table('cotisations', function (Blueprint $table) {
                if (Schema::hasColumn('cotisations', 'paiement_id')) {
                    $table->dropForeign(['paiement_id']);
                    $table->dropColumn(['paiement_id', 'statut', 'periode']);
                }
            });
        }

        // Supprimer la table paiements
        Schema::dropIfExists('paiements');
    }
};
