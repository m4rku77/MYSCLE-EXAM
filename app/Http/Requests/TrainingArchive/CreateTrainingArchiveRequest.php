<?php

declare(strict_types=1);

namespace App\Http\Requests\TrainingArchive;

use App\Models\TrainingArchive;
use Illuminate\Foundation\Http\FormRequest;

class CreateTrainingArchiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainingArchive::USER_ID => ['required', 'integer', 'exists:users,id'],
            TrainingArchive::TRAINING_PLAN_ID => ['required', 'integer', 'exists:training_plans,id'],
            TrainingArchive::COMPLETED_AT => ['required', 'date'],
            TrainingArchive::NOTES => ['nullable', 'string'],
        ];
    }
}
