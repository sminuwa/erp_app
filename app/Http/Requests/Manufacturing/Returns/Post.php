<?php

namespace App\Http\Requests\Manufacturing\Returns;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.returns.post');
    }

    public function rules()
    {
        return [];
    }
}
