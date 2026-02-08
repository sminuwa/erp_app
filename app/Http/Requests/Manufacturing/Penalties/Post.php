<?php

namespace App\Http\Requests\Manufacturing\Penalties;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.penalties.post');
    }

    public function rules()
    {
        return [];
    }
}
