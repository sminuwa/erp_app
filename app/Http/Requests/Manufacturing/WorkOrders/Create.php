<?php

namespace App\Http\Requests\Manufacturing\WorkOrders;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.work_orders.create');
    }

    public function rules()
    {
        return [];
    }
}
