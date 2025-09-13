<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('practitioners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();

            $table->timestamps();
            $table->dateTime('external_last_modified_at')->nullable();

            $table->boolean('active')->default(false);

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('specialization')->nullable();

            $table->string('practitioner_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioners');
    }
};
