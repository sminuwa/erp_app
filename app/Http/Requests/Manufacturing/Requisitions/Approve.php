<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Approve extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.approve');
    }

    public function rules()
    {
        return [];
    }
}
