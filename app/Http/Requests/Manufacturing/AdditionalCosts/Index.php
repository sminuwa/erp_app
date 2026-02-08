<?php

namespace App\Http\Requests\Manufacturing\AdditionalCosts;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.additional_costs.index');
    }

    public function rules()
    {
        return [];
    }
}
