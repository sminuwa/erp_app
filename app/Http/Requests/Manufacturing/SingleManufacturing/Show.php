<?php

namespace App\Http\Requests\Manufacturing\SingleManufacturing;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.single_manufacturing.show');
    }

    public function rules()
    {
        return [];
    }
}
