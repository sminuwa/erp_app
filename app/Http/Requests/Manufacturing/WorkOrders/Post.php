<?php

namespace App\Http\Requests\Manufacturing\WorkOrders;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.work_orders.post');
    }

    public function rules()
    {
        return [];
    }
}
