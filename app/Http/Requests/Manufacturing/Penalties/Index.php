<?php

namespace App\Http\Requests\Manufacturing\Penalties;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.penalties.index');
    }

    public function rules()
    {
        return [];
    }
}
