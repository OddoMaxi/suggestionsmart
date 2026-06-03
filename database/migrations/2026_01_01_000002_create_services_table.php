<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agence_id')->nullable()->constrained('agences')->nullOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('responsable')->nullable();
            $table->string('email_responsable')->nullable();
            $table->string('telephone_responsable')->nullable();
            $table->boolean('actif')->default(true);
            $table->string('couleur', 7)->default('#3B82F6');
            $table->string('icone')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
