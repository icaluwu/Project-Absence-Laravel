<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Karyawan') ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => ['required','in:izin,sakit,cuti'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'notes' => ['nullable','string','max:500'],
            'attachment' => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:4096'],
        ];
    }
}
