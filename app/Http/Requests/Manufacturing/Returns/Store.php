<?php

namespace App\Http\Requests\Manufacturing\Returns;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.returns.create');
    }

    public function rules()
    {
        return [
            'return_date' => ['required', 'date'],
            'production_type' => ['required', 'in:single_product,batch_conversion'],
            'production_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string'],
            'return_qty' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}
