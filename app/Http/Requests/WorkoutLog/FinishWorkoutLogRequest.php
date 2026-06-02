<?php

declare(strict_types=1);

namespace App\Http\Requests\WorkoutLog;

use Illuminate\Foundation\Http\FormRequest;

class FinishWorkoutLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duration_seconds'           => ['required', 'integer', 'min:0'],
            'sets'                       => ['required', 'array', 'min:1'],
            'sets.*.exercise_name'       => ['required', 'string', 'max:255'],
            'sets.*.set_number'          => ['required', 'integer', 'min:1'],
            'sets.*.reps'                => ['required', 'integer', 'min:0'],
            'sets.*.weight'              => ['required', 'numeric', 'min:0'],
        ];
    }
}