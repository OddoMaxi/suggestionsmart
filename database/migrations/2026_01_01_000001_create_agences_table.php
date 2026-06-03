<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('ville')->nullable();
            $table->string('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('actif')->default(true);
            $table->string('logo')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agences');
    }
};
