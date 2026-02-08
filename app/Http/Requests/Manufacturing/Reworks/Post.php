<?php

namespace App\Http\Requests\Manufacturing\Reworks;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.reworks.post');
    }

    public function rules()
    {
        return [];
    }
}
