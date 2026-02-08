<?php

namespace App\Http\Requests\Manufacturing\Penalties;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.penalties.show');
    }

    public function rules()
    {
        return [];
    }
}
