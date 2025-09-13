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
        Schema::create('endpoints', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('root');
            $table->string('user')->nullable();
            $table->string('token', 1000)->nullable();
            $table->string('type');
            $table->string('target');

            $table->numericMorphs('group');
 
            $table->unique(['group_id', 'group_type', 'root']);
            $table->index('root');
            $table->index('type');
            $table->index('target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endpoints');
    }
};
