<?php

namespace App\Http\Requests\Manufacturing\DailySchedules;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.schedules.create');
    }

    public function rules()
    {
        return [
            'schedule_date' => ['required', 'date'],
            'order_id' => ['required', 'exists:production_orders,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.order_item_id' => ['required', 'exists:production_order_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
        ];
    }
}
