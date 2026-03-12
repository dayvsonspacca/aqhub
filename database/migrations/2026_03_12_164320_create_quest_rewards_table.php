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
        Schema::create('quest_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quest_id')->constrained('quests')->cascadeOnDelete();

            $table->string('type'); // experience | gold | item | reputation

            // ExperienceReward | GoldReward | ReputationReward (amount)
            $table->unsignedInteger('amount')->nullable();

            // ItemReward
            $table->foreignId('item_id')
                ->nullable()
                ->constrained('items')
                ->nullOnDelete();
            $table->unsignedTinyInteger('rate')->nullable();    // 1-100
            $table->unsignedInteger('quantity')->nullable();

            // ReputationReward (faction)
            $table->foreignId('faction_id')
                ->nullable()
                ->constrained('factions')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quest_rewards');
    }
};
