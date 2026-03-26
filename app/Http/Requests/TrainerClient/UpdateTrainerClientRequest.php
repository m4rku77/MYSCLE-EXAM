<?php

declare(strict_types=1);

namespace App\Http\Requests\TrainerClient;

use App\Models\TrainerClient;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainerClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainerClient::TRAINER_ID => ['sometimes', 'integer', 'exists:users,id'],
            TrainerClient::ATHLETE_ID => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }
}
