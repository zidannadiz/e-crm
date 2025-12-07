<?php

namespace App\Http\Requests\Ecrm;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
        $clientId = $this->route('client') ? $this->route('client')->id : null;
        
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:ecrm_clients,email,' . $clientId,
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tipe' => 'required|in:individu,perusahaan',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama client harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'tipe.required' => 'Tipe client harus dipilih',
        ];
    }
}
