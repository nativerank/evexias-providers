<?php

use App\PracticeStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('practitioner_types', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();

            $table->timestamps();
            $table->dateTime('external_last_modified_at');

            $table->boolean('active')->default(false);

            $table->string('name');
            $table->enum('status', PracticeStatus::cases());

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practitioner_types');
    }
};
