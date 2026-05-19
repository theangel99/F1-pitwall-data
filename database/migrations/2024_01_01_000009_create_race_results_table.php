<?php

declare(strict_types=1);

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
        Schema::create('race_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('race_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('driver_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->nullable();
            $table->integer('points')->default(0);
            $table->string('status')->nullable();
            $table->float('fastest_lap', 10, 3)->nullable();
            $table->boolean('fastest_lap_point')->default(false);
            $table->timestamps();

            $table->index('race_id');
            $table->index('driver_id');
            $table->index(['race_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_results');
    }
};
