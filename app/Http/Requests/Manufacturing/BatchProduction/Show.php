<?php

namespace App\Http\Requests\Manufacturing\BatchProduction;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_production.show');
    }

    public function rules()
    {
        return [];
    }
}
