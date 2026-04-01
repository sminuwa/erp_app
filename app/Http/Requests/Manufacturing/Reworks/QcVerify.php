<?php

namespace App\Http\Requests\Manufacturing\Reworks;

use Illuminate\Foundation\Http\FormRequest;

class QcVerify extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.reworks.qc_verify');
    }

    public function rules()
    {
        return [];
    }
}
