<?php

declare(strict_types=1);

namespace App\Http\Requests\Exercise;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Exercise;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Exercise::NAME => ['sometimes', 'string', 'max:255'],
            Exercise::SETS => ['sometimes', 'integer'],
            Exercise::REPS => ['sometimes', 'integer'],
            Exercise::WEIGHT => ['nullable', 'numeric'],
            Exercise::NOTES => ['nullable', 'string'],
        ];
    }
}