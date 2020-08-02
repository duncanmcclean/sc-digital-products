<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'license_key' => 'required|string',
        ];
    }
}