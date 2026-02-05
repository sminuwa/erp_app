<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.create');
    }

    public function rules()
    {
        return [];
    }
}
