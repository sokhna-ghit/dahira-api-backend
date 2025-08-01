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
            $table->string('confrerie')->nullable()->after('ville');
            $table->text('description')->nullable()->after('confrerie');
            $table->string('imageUrl')->nullable()->after('description');
            $table->integer('nombreMembres')->default(0)->after('imageUrl');
            $table->string('region')->nullable()->after('ville');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dahiras', function (Blueprint $table) {
            $table->dropColumn(['confrerie', 'description', 'imageUrl', 'nombreMembres', 'region']);
        });
    }
};
