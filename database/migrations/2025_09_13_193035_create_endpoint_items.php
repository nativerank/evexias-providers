<?php

use App\Models\Endpoint;
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
        Schema::create('endpoint_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('source_hash');
            $table->string('target_hash')->nullable();
            $table->unsignedBigInteger('external_id')->nullable();

            $table->numericMorphs('item');
            $table->foreignIdFor(Endpoint::class)->constrained()->cascadeOnDelete();

            $table->timestamp('modified_at')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->index(['source_hash', 'target_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endpoint_items');
    }
};
