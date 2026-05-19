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
        Schema::create('f1_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('race_id')->constrained()->cascadeOnDelete();
            $table->integer('openf1_session_key')->unique();
            $table->string('type');
            $table->timestamp('starts_at');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index('race_id');
            $table->index('openf1_session_key');
            $table->index('type');
            $table->index('starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f1_sessions');
    }
};
