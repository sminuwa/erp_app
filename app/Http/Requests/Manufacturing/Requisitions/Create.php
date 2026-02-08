<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.create');
    }

    public function rules()
    {
        return [];
    }
}
