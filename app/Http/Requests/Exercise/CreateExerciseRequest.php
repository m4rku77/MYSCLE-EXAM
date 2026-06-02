<?php

declare(strict_types=1);

namespace App\Http\Requests\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class CreateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workout_id'           => ['required', 'integer', 'exists:training_plans,id'],
            'name'                 => ['required', 'string', 'max:255'],
            'notes'                => ['nullable', 'string', 'max:1000'],
            'sets_data'            => ['nullable', 'array'],
            'sets_data.*.reps'     => ['nullable', 'integer', 'min:0'],
            'sets_data.*.weight'   => ['nullable', 'numeric', 'min:0'],
        ];
    }
}