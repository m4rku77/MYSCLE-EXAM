<?php

declare(strict_types=1);

namespace App\Http\Requests\TrainingPlan;

use App\Models\TrainingPlan;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainingPlan::NAME        => ['sometimes', 'string', 'max:255'],
            TrainingPlan::IS_FAVORITE => ['sometimes', 'boolean'],
            TrainingPlan::NOTES       => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}