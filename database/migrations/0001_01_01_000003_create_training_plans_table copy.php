<?php

use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(TrainingPlan::TABLE, function (Blueprint $table) {
            $table->id();

            $table->foreignId(TrainingPlan::USER_ID)
                ->constrained(User::TABLE)
                ->cascadeOnDelete();

            $table->string(TrainingPlan::NAME, 50);

            $table->boolean(TrainingPlan::IS_FAVORITE)
                ->default(false);

            $table->timestamp(TrainingPlan::CREATED_AT)
                ->useCurrent();

            $table->timestamp(TrainingPlan::UPDATED_AT)
                ->useCurrent()
                ->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(TrainingPlan::TABLE);
    }
};