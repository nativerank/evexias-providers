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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->string('formatted_address');
            $table->string('subpremise')->nullable();
            $table->string('street_number')->nullable();
            $table->string('route')->nullable();
            $table->string('locality')->nullable();
            $table->string('administrative_area_level_1')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('postal_code_suffix')->nullable();
            $table->json('metadata')->nullable();

            $table->string('place_id');

            $table->morphs('locatable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
