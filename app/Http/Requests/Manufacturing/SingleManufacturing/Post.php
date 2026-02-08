<?php

namespace App\Http\Requests\Manufacturing\SingleManufacturing;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.single_manufacturing.post');
    }

    public function rules()
    {
        return [];
    }
}
