<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Izinkan semua role pegawai untuk mengajukan lembur
        return $this->user()?->hasAnyRole(['Admin','HR','Karyawan']) ?? false;
    }

    public function rules(): array
    {
        return [
            'date' => ['required','date'],
            'hours' => ['required','numeric','min:0.5','max:12'],
            'reason' => ['nullable','string','max:255'],
        ];
    }
}
