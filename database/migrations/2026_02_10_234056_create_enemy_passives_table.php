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
        Schema::create('enemy_passives', function (Blueprint $table) {
            $table->id();
            $table->string('description')->unique();
            $table->timestamps();
        });

        Schema::create('enemy_passive_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enemy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enemy_passive_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['enemy_id', 'enemy_passive_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enemy_passives');
        Schema::dropIfExists('enemy_passive_assignments');
    }
};
