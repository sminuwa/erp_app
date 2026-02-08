<?php

namespace App\Http\Requests\Manufacturing\BatchConversions;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_conversion.create');
    }

    public function rules()
    {
        return [];
    }
}
