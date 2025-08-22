<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin','HR']) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:150','unique:users,email'],
            'password' => ['nullable','string','min:6'],
            'nik' => ['nullable','string','max:50','unique:users,nik'],
            'departemen' => ['nullable','string','max:100'],
            'jabatan' => ['nullable','string','max:100'],
            'tanggal_masuk' => ['nullable','date'],
            'gaji_pokok' => ['nullable','numeric'],
            'status_karyawan' => ['nullable','string','max:50'],
        ];
    }
}
