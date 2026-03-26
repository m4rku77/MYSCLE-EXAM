<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Subscription::USER_ID => ['required', 'integer', 'exists:users,id'],
            Subscription::TYPE => ['required', 'string', 'max:50'],
            Subscription::START_DATE => ['required', 'date'],
            Subscription::END_DATE => ['nullable', 'date'],
            Subscription::STATUS => ['required', 'string'],
        ];
    }
}
