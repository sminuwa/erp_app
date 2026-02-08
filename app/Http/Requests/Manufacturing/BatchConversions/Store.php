<?php

namespace App\Http\Requests\Manufacturing\BatchConversions;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_conversion.create');
    }

    public function rules()
    {
        return [
            'conversion_date' => ['required', 'date'],
            'batch_production_id' => ['required', 'exists:batch_productions,id'],
            'produced_qty' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}
