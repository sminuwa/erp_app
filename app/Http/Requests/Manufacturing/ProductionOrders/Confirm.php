<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Confirm extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.confirm');
    }

    public function rules()
    {
        return [];
    }
}
