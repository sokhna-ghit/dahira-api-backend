<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('membres', function (Blueprint $table) {
    $table->id();
    $table->string('nom');
    $table->string('prenom');
    $table->string('email')->unique();
    $table->string('telephone')->nullable();
    $table->string('adresse')->nullable();
    $table->string('genre')->nullable();
    $table->date('date_naissance')->nullable();
    $table->unsignedBigInteger('dahira_id');
    $table->boolean('active')->default(true);
    $table->timestamps();

});

        
    }

    public function down()
    {
        Schema::dropIfExists('membres');
    }
};
