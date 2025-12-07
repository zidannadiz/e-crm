<?php

namespace App\Http\Requests\Ecrm;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:ecrm_clients,id',
            'jenis_desain' => 'required|in:logo,branding,web_design,ui_ux,print_design,packaging,social_media,seminar,lainnya',
            'deskripsi' => 'required|string|min:10',
            'kebutuhan' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'status' => 'sometimes|in:pending,approved,in_progress,review,completed,cancelled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Client harus dipilih',
            'client_id.exists' => 'Client tidak valid',
            'jenis_desain.required' => 'Jenis desain harus dipilih',
            'deskripsi.required' => 'Deskripsi order harus diisi',
            'deskripsi.min' => 'Deskripsi minimal 10 karakter',
            'deadline.after' => 'Deadline harus setelah hari ini',
        ];
    }
}
