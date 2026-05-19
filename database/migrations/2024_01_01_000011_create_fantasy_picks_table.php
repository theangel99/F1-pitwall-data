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
        Schema::create('fantasy_picks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fantasy_team_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('driver_id')->constrained()->cascadeOnDelete();
            $table->integer('points_earned')->default(0);
            $table->timestamps();

            $table->index('fantasy_team_id');
            $table->index('driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fantasy_picks');
    }
};
