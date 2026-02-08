<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Issue extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.issue');
    }

    public function rules()
    {
        return [];
    }
}
