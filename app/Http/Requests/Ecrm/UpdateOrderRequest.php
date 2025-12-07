<?php

namespace App\Http\Requests\Ecrm;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,approved,in_progress,review,completed,cancelled',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date',
            'catatan_admin' => 'nullable|string',
        ];
    }
}
