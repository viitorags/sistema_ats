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
        Schema::create('resume', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('filename');
            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_phone')->nullable();
            $table->integer('score')->nullable();
            $table->integer('technical_score')->nullable();
            $table->integer('match_score')->nullable();
            $table->text('summary')->nullable();
            $table->json('skills')->nullable();
            $table->string('category')->nullable();
            $table->integer('processing_time_ms')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume');
    }
};
