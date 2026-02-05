<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.show');
    }

    public function rules()
    {
        return [];
    }
}
