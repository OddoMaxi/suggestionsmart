<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('activitylog.database_connection'))->create(
            config('activitylog.table_name'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable()->index();
                $table->text('description');
                $table->string('event')->nullable();

                // UUID-compatible morph columns (string au lieu de bigint)
                $table->string('subject_type')->nullable();
                $table->string('subject_id', 36)->nullable();
                $table->index(['subject_type', 'subject_id'], 'subject');

                $table->string('causer_type')->nullable();
                $table->string('causer_id', 36)->nullable();
                $table->index(['causer_type', 'causer_id'], 'causer');

                $table->json('properties')->nullable();
                $table->uuid('batch_uuid')->nullable();
                $table->timestamps();
            });
    }

    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->dropIfExists(config('activitylog.table_name'));
    }
};
