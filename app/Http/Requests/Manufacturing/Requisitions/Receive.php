<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Receive extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.receive');
    }

    public function rules()
    {
        return [];
    }
}
