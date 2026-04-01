<?php

namespace App\Http\Requests\Manufacturing\WorkOrders;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.work_orders.show');
    }

    public function rules()
    {
        return [];
    }
}
