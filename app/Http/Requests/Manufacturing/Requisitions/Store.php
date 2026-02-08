<?php

namespace App\Http\Requests\Manufacturing\Requisitions;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.requisitions.create');
    }

    public function rules()
    {
        return [
            'reference' => ['required', 'unique:materials_requisitions,reference'],
            'requisition_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
