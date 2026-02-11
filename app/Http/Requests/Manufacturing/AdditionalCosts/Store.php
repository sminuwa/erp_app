<?php

namespace App\Http\Requests\Manufacturing\AdditionalCosts;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.additional_costs.create');
    }

    public function rules()
    {
        return [
            'reference' => ['required', 'unique:manufacturing_additional_costs,reference'],
            'cost_date' => ['required', 'date'],
            'production_type' => ['required', 'in:single_product,batch_conversion'],
            'account_id' => ['required', 'exists:general_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->production_type === 'single_product') {
                if (empty($this->single_manufacturing_id)) {
                    $validator->errors()->add('single_manufacturing_id', 'Single Manufacturing is required when production type is single_product.');
                }
            } elseif ($this->production_type === 'batch_conversion') {
                if (empty($this->batch_production_id)) {
                    $validator->errors()->add('batch_production_id', 'Batch Conversion is required when production type is batch_conversion.');
                }
            }
        });
    }
}
