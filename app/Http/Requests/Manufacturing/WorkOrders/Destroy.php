<?php

namespace App\Http\Requests\Manufacturing\WorkOrders;

use Illuminate\Foundation\Http\FormRequest;

class Destroy extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.work_orders.delete');
    }

    public function rules()
    {
        return [];
    }
}
