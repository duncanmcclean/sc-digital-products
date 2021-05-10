<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadRequest extends FormRequest
{
    public function authorize()
    {
        return $this->hasValidSignature();
    }

    public function rules()
    {
        return [];
    }
}
