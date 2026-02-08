<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.show');
    }

    public function rules()
    {
        return [];
    }
}
