<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.index');
    }

    public function rules()
    {
        return [];
    }
}
