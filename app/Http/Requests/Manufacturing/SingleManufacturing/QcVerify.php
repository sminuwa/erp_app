<?php

namespace App\Http\Requests\Manufacturing\SingleManufacturing;

use Illuminate\Foundation\Http\FormRequest;

class QcVerify extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.single_manufacturing.qc_verify');
    }

    public function rules()
    {
        return [];
    }
}
