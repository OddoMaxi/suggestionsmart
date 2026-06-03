<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('agence_id')->nullable()->constrained('agences')->nullOnDelete()->after('id');
            $table->foreignUuid('service_id')->nullable()->constrained('services')->nullOnDelete()->after('agence_id');
            $table->string('telephone', 30)->nullable()->after('email');
            $table->boolean('actif')->default(true)->after('telephone');
            $table->string('avatar')->nullable()->after('actif');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['agence_id', 'service_id', 'telephone', 'actif', 'avatar']);
        });
    }
};
