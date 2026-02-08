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
            'cost_date' => ['required', 'date'],
            'production_type' => ['required', 'in:single_product,batch_conversion'],
            'production_id' => ['required', 'integer', 'min:1'],
            'account_id' => ['required', 'exists:general_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ];
    }
}
