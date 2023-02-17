<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'packageName' => ['required'],
            'packageHash' => ['required'],
            'packageHost' => ['required', 'in:s3,local'],
            'packagePath' => ['nullable'],
        ];
    }
}
