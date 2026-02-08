<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.index');
    }

    public function rules()
    {
        return [];
    }
}
