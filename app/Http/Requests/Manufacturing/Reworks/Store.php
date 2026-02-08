<?php

namespace App\Http\Requests\Manufacturing\Reworks;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.reworks.create');
    }

    public function rules()
    {
        return [
            'rework_date' => ['required', 'date'],
            'production_type' => ['required', 'in:single_product,batch_conversion'],
            'production_id' => ['required', 'integer', 'min:1'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'power_cost' => ['nullable', 'numeric', 'min:0'],
            'other_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
