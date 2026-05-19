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
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('constructor_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('openf1_driver_number');
            $table->string('openf1_driver_id')->unique();
            $table->string('code', 3);
            $table->string('full_name');
            $table->string('nationality')->nullable();
            $table->string('headshot_url')->nullable();
            $table->timestamps();

            $table->index('constructor_id');
            $table->index('openf1_driver_number');
            $table->index('openf1_driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
