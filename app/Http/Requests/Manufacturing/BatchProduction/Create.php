<?php

namespace App\Http\Requests\Manufacturing\BatchProduction;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_production.create');
    }

    public function rules()
    {
        return [];
    }
}
