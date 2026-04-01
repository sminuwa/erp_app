<?php

namespace App\Http\Requests\Manufacturing\BatchProduction;

use Illuminate\Foundation\Http\FormRequest;

class QcVerify extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_production.qc_verify');
    }

    public function rules()
    {
        return [];
    }
}
