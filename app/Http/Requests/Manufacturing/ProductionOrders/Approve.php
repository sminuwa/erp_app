<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Approve extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.approve');
    }

    public function rules()
    {
        return [];
    }
}
