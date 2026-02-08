<?php

namespace App\Http\Requests\Manufacturing\BatchConversions;

use Illuminate\Foundation\Http\FormRequest;

class Destroy extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_conversion.delete');
    }

    public function rules()
    {
        return [];
    }
}
