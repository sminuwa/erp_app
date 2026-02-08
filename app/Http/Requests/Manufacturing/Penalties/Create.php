<?php

namespace App\Http\Requests\Manufacturing\Penalties;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.penalties.create');
    }

    public function rules()
    {
        return [];
    }
}
