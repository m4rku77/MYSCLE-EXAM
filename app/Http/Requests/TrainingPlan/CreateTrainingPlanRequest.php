<?php

declare(strict_types=1);

namespace App\Http\Requests\TrainingPlan;

use App\Models\TrainingPlan;
use Illuminate\Foundation\Http\FormRequest;

class CreateTrainingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainingPlan::USER_ID => ['required', 'integer', 'exists:users,id'],
            TrainingPlan::NAME => ['required', 'string', 'max:255'],
            TrainingPlan::IS_FAVORITE => ['boolean'],
        ];
    }
}
