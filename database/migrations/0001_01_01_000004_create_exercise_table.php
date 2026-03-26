<?php

use App\Models\Exercise;
use App\Models\TrainingPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(Exercise::TABLE, function (Blueprint $table) {

            $table->id();

            $table->foreignId(Exercise::TRAINING_PLAN_ID)
                ->constrained(TrainingPlan::TABLE)
                ->cascadeOnDelete();

            $table->string(Exercise::NAME, 100);

            $table->integer(Exercise::SETS);
            $table->integer(Exercise::REPS);

            $table->decimal(Exercise::WEIGHT, 6, 2)
                ->nullable();

            $table->text(Exercise::NOTES)
                ->nullable();

            $table->timestamp(Exercise::CREATED_AT)
                ->useCurrent();

            $table->timestamp(Exercise::UPDATED_AT)
                ->useCurrent()
                ->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(Exercise::TABLE);
    }
};
