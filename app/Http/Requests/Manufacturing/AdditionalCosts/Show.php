<?php

namespace App\Http\Requests\Manufacturing\AdditionalCosts;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.additional_costs.show');
    }

    public function rules()
    {
        return [];
    }
}
