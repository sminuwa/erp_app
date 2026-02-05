<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.create');
    }

    public function rules()
    {
        return [
            'reference' => 'required|max:50|unique:production_orders,reference',
            'order_date' => 'required|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|max:1000',
            'items' => 'required|array|min:1',
            'items.*.bom_id' => 'required|exists:manufacturing_boms,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'At least one BOM item is required.',
            'items.min' => 'At least one BOM item is required.',
            'items.*.bom_id.required' => 'Each item must have a BOM selected.',
            'items.*.quantity.required' => 'Each item must have a quantity.',
        ];
    }
}
