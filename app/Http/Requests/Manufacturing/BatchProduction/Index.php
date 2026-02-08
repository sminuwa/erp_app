<?php

namespace App\Http\Requests\Manufacturing\BatchProduction;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_production.index');
    }

    public function rules()
    {
        return [];
    }
}
