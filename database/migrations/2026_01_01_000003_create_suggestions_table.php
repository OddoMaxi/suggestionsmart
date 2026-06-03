<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suggestions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignUuid('agence_id')->nullable()->constrained('agences')->nullOnDelete();

            // Auteur
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone', 30);
            $table->string('email')->nullable();

            // Contenu
            $table->enum('type', ['suggestion', 'critique', 'reclamation', 'felicitation'])->default('suggestion');
            $table->text('message');
            $table->tinyInteger('satisfaction')->nullable()->comment('Note 1-5');

            // Traitement
            $table->enum('statut', ['nouveau', 'en_cours', 'traite', 'cloture'])->default('nouveau');
            $table->enum('priorite', ['normale', 'haute'])->default('normale');
            $table->foreignId('assigne_a')->nullable()->constrained('users')->nullOnDelete();
            $table->text('commentaire_interne')->nullable();

            // Métadonnées
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('canal')->default('qr_code')->comment('qr_code, web, direct');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'priorite']);
            $table->index(['service_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
