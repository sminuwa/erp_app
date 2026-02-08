<?php

namespace App\Http\Requests\Manufacturing\BatchConversions;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_conversion.post');
    }

    public function rules()
    {
        return [];
    }
}
