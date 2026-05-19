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
        Schema::create('races', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained()->cascadeOnDelete();
            $table->integer('openf1_meeting_key')->unique();
            $table->string('name');
            $table->string('circuit');
            $table->string('country');
            $table->string('location')->nullable();
            $table->timestamp('date');
            $table->integer('round_number')->nullable();
            $table->timestamps();

            $table->index('season_id');
            $table->index('openf1_meeting_key');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
