<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Subscription::TYPE => ['sometimes', 'string'],
            Subscription::START_DATE => ['sometimes', 'date'],
            Subscription::END_DATE => ['nullable', 'date'],
            Subscription::STATUS => ['sometimes', 'string'],
        ];
    }
}
