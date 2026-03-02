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
        Schema::create('maps', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('aqw_id')->unique();
            $table->string('name')->index();
            $table->string('join_name')->unique();

            $table->text('description')->nullable();

            $table->boolean('upgrade_only')->default(false);

            $table->unsignedInteger('recommended_min_level')->nullable();
            $table->unsignedInteger('recommended_max_level')->nullable();

            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('map_monster_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('map_id')
                ->constrained('maps')
                ->cascadeOnDelete();

            $table->foreignId('monster_id')
                ->constrained('monsters')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['map_id', 'monster_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_monster_assignments');
        Schema::dropIfExists('maps');
    }
};
