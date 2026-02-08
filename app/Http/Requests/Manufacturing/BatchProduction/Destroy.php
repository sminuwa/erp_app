<?php

namespace App\Http\Requests\Manufacturing\BatchProduction;

use Illuminate\Foundation\Http\FormRequest;

class Destroy extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_production.delete');
    }

    public function rules()
    {
        return [];
    }
}
