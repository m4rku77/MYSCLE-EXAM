<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TrainingPlan;

class UpdateTrainingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainingPlan::NAME => ['sometimes', 'string', 'max:255'],
            TrainingPlan::IS_FAVORITE => ['sometimes', 'boolean'],
        ];
    }
}