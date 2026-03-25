<?php

declare(strict_types=1);

namespace App\Http\Requests\TrainerClient;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TrainerClient;

class CreateTrainerClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            TrainerClient::TRAINER_ID => ['required', 'integer', 'exists:users,id'],
            TrainerClient::ATHLETE_ID => ['required', 'integer', 'exists:users,id'],
        ];
    }
}