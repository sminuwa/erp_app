<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Destroy extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.delete');
    }

    public function rules()
    {
        return [];
    }
}
