<?php

namespace App\Http\Requests\Manufacturing\AdditionalCosts;

use Illuminate\Foundation\Http\FormRequest;

class Post extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.additional_costs.post');
    }

    public function rules()
    {
        return [];
    }
}
