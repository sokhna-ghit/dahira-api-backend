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
        Schema::table('membres', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('membres', 'profession')) {
                $table->string('profession')->nullable()->after('date_naissance');
            }
            if (!Schema::hasColumn('membres', 'statut')) {
                $table->enum('statut', ['actif', 'inactif', 'suspendu'])->default('actif')->after('profession');
            }
            if (!Schema::hasColumn('membres', 'commentaires')) {
                $table->text('commentaires')->nullable()->after('statut');
            }
            if (!Schema::hasColumn('membres', 'date_inscription')) {
                $table->date('date_inscription')->default(now())->after('commentaires');
            }
            if (!Schema::hasColumn('membres', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('dahira_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('membres', function (Blueprint $table) {
            $table->dropColumn(['profession', 'statut', 'commentaires', 'date_inscription']);
            if (Schema::hasColumn('membres', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
