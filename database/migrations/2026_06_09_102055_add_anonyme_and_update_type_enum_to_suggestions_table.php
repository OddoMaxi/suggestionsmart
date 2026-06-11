<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->boolean('anonyme')->default(false)->after('agence_id');
            $table->string('nom')->nullable()->change();
            $table->string('prenom')->nullable()->change();
            $table->string('telephone', 30)->nullable()->change();
        });

        // PostgreSQL : supprimer 'reclamation' de l'enum type
        // Migrer d'abord les données existantes
        DB::statement("UPDATE suggestions SET type = 'critique' WHERE type = 'reclamation'");

        // Recréer la colonne sans reclamation (PostgreSQL ne supporte pas DROP ENUM VALUE)
        DB::statement("ALTER TABLE suggestions ALTER COLUMN type TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE suggestions DROP CONSTRAINT IF EXISTS suggestions_type_check");
        DB::statement("ALTER TABLE suggestions ADD CONSTRAINT suggestions_type_check CHECK (type IN ('suggestion', 'critique', 'felicitation'))");
    }

    public function down(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropColumn('anonyme');
            $table->string('nom')->nullable(false)->change();
            $table->string('prenom')->nullable(false)->change();
            $table->string('telephone', 30)->nullable(false)->change();
        });

        DB::statement("ALTER TABLE suggestions DROP CONSTRAINT IF EXISTS suggestions_type_check");
        DB::statement("ALTER TABLE suggestions ALTER COLUMN type TYPE VARCHAR(20)");
        DB::statement("ALTER TABLE suggestions ADD CONSTRAINT suggestions_type_check CHECK (type IN ('suggestion', 'critique', 'reclamation', 'felicitation'))");
    }
};
