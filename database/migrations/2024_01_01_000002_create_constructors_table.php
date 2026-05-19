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
        Schema::create('constructors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('openf1_team_name')->unique();
            $table->string('name');
            $table->string('nationality')->nullable();
            $table->string('color_hex', 6)->nullable();
            $table->timestamps();

            $table->index('openf1_team_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constructors');
    }
};
