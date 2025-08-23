<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin','HR']) ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        return [
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:150','unique:users,email,'.$userId],
            'password' => ['nullable','string','min:6'],
            'nik' => ['nullable','string','max:50','unique:users,nik,'.$userId],
            'departemen' => ['nullable','string','max:100'],
            'jabatan' => ['nullable','string','max:100'],
            'tanggal_masuk' => ['nullable','date'],
            'gaji_pokok' => ['nullable','numeric'],
            'status_karyawan' => ['nullable','string','max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
'email.unique' => 'Email tidak boleh sama.',
            'password.min' => 'Password minimal :min karakter.',
'nik.unique' => 'NIK sudah ada.',
            'gaji_pokok.numeric' => 'Gaji Pokok harus berupa angka.',
        ];
    }
}
