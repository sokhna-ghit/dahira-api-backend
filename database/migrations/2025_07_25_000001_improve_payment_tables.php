<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Amélioration table paiements
        Schema::table('paiements', function (Blueprint $table) {
            // Nouvelles colonnes si elles n'existent pas
            if (!Schema::hasColumn('paiements', 'membre_id')) {
                $table->unsignedBigInteger('membre_id')->nullable()->after('id');
                $table->foreign('membre_id')->references('id')->on('membres')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('paiements', 'dahira_id')) {
                $table->unsignedBigInteger('dahira_id')->nullable()->after('membre_id');
                $table->foreign('dahira_id')->references('id')->on('dahiras')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('paiements', 'montant')) {
                $table->decimal('montant', 10, 2)->nullable()->after('dahira_id');
            }
            
            if (!Schema::hasColumn('paiements', 'telephone')) {
                $table->string('telephone')->nullable()->after('montant');
            }
            
            if (!Schema::hasColumn('paiements', 'operateur')) {
                $table->enum('operateur', ['orange', 'free', 'wave'])->nullable()->after('telephone');
            }
            
            if (!Schema::hasColumn('paiements', 'type_cotisation')) {
                $table->enum('type_cotisation', ['mensuelle', 'annuelle', 'evenement', 'zakat'])->nullable()->after('operateur');
            }
            
            if (!Schema::hasColumn('paiements', 'description')) {
                $table->text('description')->nullable()->after('type_cotisation');
            }
            
            if (!Schema::hasColumn('paiements', 'statut')) {
                $table->enum('statut', ['en_attente', 'reussi', 'echoue', 'annule'])->default('en_attente')->after('description');
            }
            
            if (!Schema::hasColumn('paiements', 'method_paiement')) {
                $table->string('method_paiement')->default('mobile_money')->after('statut');
            }
            
            if (!Schema::hasColumn('paiements', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('method_paiement');
            }
            
            if (!Schema::hasColumn('paiements', 'date_paiement')) {
                $table->timestamp('date_paiement')->nullable()->after('transaction_id');
            }
        });

        // Amélioration table cotisations
        Schema::table('cotisations', function (Blueprint $table) {
            if (!Schema::hasColumn('cotisations', 'paiement_id')) {
                $table->unsignedBigInteger('paiement_id')->nullable()->after('date_paiement');
                $table->foreign('paiement_id')->references('id')->on('paiements')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('cotisations', 'statut')) {
                $table->enum('statut', ['en_attente', 'paye', 'en_retard', 'annule'])->default('en_attente')->after('paiement_id');
            }
            
            if (!Schema::hasColumn('cotisations', 'description')) {
                $table->text('description')->nullable()->after('statut');
            }
            
            if (!Schema::hasColumn('cotisations', 'periode')) {
                $table->string('periode')->nullable()->after('description'); // ex: "2025-01" pour janvier 2025
            }

            if (!Schema::hasColumn('cotisations', 'type')) {
                $table->string('type')->default('mensuelle')->after('periode');
            }
        });
    }

    public function down()
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['membre_id']);
            $table->dropForeign(['dahira_id']);
            $table->dropColumn([
                'membre_id', 'dahira_id', 'montant', 'telephone', 'operateur',
                'type_cotisation', 'description', 'statut', 'method_paiement',
                'transaction_id', 'date_paiement'
            ]);
        });

        Schema::table('cotisations', function (Blueprint $table) {
            $table->dropForeign(['paiement_id']);
            $table->dropColumn(['paiement_id', 'statut', 'description', 'periode', 'type']);
        });
    }
};
