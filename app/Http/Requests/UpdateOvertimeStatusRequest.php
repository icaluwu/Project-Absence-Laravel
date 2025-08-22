<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOvertimeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Admin','HR']) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required','in:approved,rejected'],
        ];
    }
}
