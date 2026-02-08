<?php

namespace App\Http\Requests\Manufacturing\Reworks;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.reworks.show');
    }

    public function rules()
    {
        return [];
    }
}
