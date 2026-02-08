<?php

namespace App\Http\Requests\Manufacturing\BatchConversions;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_conversion.index');
    }

    public function rules()
    {
        return [];
    }
}
