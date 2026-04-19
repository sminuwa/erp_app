<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Adjust extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.adjust');
    }

    public function rules()
    {
        return [];
    }
}
