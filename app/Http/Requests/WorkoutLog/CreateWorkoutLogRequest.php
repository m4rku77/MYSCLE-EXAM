<?php

declare(strict_types=1);

namespace App\Http\Requests\WorkoutLog;

use Illuminate\Foundation\Http\FormRequest;

class CreateWorkoutLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'training_plan_id' => ['required', 'integer', 'exists:training_plans,id'],
        ];
    }
}