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
            TrainingPlan::NAME        => ['required', 'string', 'max:255'],
            'exercises'               => ['nullable', 'array'],
            'exercises.*.name'        => ['required', 'string', 'max:255'],
            'exercises.*.sets'        => ['nullable', 'array'],
            'exercises.*.sets.*.reps'   => ['required', 'integer', 'min:0'],
            'exercises.*.sets.*.weight' => ['required', 'numeric', 'min:0'],
        ];
    }
}