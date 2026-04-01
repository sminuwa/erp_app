<?php

namespace App\Http\Requests\Manufacturing\ProductionOrders;

use Illuminate\Foundation\Http\FormRequest;

class Reject extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.production_orders.reject');
    }

    public function rules()
    {
        return [];
    }
}
