<?php

namespace App\Http\Requests\Manufacturing\Penalties;

use Illuminate\Foundation\Http\FormRequest;

class Destroy extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.penalties.delete');
    }

    public function rules()
    {
        return [];
    }
}
