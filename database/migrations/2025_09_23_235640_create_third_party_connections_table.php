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
        Schema::create('third_party_connections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('provider');
            $table->string('class')->nullable();
            $table->string('external_id');
            $table->morphs('connectable');

            $table->unique(['connectable_type', 'connectable_id', 'provider', 'external_id'], 'third_party_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_party_connections');
    }
};
