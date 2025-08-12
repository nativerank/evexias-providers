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
        Schema::create('practice_sales_rep', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Practice::class);
            $table->foreignIdFor(\App\Models\SalesRep::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_sales_rep');
    }
};
