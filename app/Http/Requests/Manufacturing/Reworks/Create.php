<?php

namespace App\Http\Requests\Manufacturing\Reworks;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.reworks.create');
    }

    public function rules()
    {
        return [];
    }
}
