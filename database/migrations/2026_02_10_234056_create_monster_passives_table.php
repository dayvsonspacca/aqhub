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
        Schema::create('Monster_passives', function (Blueprint $table) {
            $table->id();
            $table->string('description')->unique();
            $table->timestamps();
        });

        Schema::create('Monster_passive_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('Monster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('Monster_passive_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['Monster_id', 'Monster_passive_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Monster_passives');
        Schema::dropIfExists('Monster_passive_assignments');
    }
};
