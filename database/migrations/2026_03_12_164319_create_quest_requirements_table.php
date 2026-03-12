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
        Schema::create('quest_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained('quests')->cascadeOnDelete();

            $table->string('type'); // level | reputation | class_rank | item | quest

            // LevelRequirement
            $table->unsignedSmallInteger('required_level')->nullable();

            // ReputationRequirement
            $table->foreignId('faction_id')
                ->nullable()
                ->constrained('factions')
                ->nullOnDelete();
            $table->unsignedTinyInteger('required_rank')->nullable();

            // ClassRankRequirement
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('character_classes')
                ->nullOnDelete();
            $table->unsignedTinyInteger('required_class_rank')->nullable();

            // ItemRequirement
            $table->foreignId('required_item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();

            // QuestRequirement
            $table->foreignId('required_quest_id')
                ->nullable()
                ->constrained('quests')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_requirements');
    }
};
