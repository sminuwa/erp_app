<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Close extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.close');
    }

    public function rules()
    {
        return [];
    }
}
