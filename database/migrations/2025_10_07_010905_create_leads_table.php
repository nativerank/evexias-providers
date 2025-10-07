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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->json('data');
            $table->string('source')->nullable();
            $table->string('status')->default('new');
            $table->timestamp('lead_created_at')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();

            $table->index(['practice_id', 'created_at']);
            $table->index(['first_name', 'last_name']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
