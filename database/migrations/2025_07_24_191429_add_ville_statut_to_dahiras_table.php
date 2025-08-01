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
        Schema::table('dahiras', function (Blueprint $table) {
            $table->string('ville')->nullable()->after('nom');
            $table->enum('statut', ['actif', 'inactif'])->default('actif')->after('adresse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dahiras', function (Blueprint $table) {
            $table->dropColumn(['ville', 'statut']);
        });
    }
};
