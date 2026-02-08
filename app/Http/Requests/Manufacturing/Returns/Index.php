<?php

namespace App\Http\Requests\Manufacturing\Returns;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.returns.index');
    }

    public function rules()
    {
        return [];
    }
}
