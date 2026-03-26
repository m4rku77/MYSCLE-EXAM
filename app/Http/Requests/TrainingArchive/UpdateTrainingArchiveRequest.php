<?php

declare(strict_types=1);

namespace App\Http\Requests\TrainingArchive;

use App\Models\TrainingArchive;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingArchiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainingArchive::COMPLETED_AT => ['sometimes', 'date'],
            TrainingArchive::NOTES => ['nullable', 'string'],
        ];
    }
}
