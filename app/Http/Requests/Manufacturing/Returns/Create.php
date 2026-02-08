<?php

namespace App\Http\Requests\Manufacturing\Returns;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.returns.create');
    }

    public function rules()
    {
        return [];
    }
}
