<?php

namespace App\Http\Requests\Manufacturing\SingleManufacturing;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.single_manufacturing.create');
    }

    public function rules()
    {
        return [
            'manufacturing_date' => ['required', 'date'],
            'requisition_id' => ['nullable', 'exists:materials_requisitions,id'],
            'team_id' => ['required', 'exists:manufacturing_teams,id'],
            'bom_id' => ['required', 'exists:manufacturing_boms,id'],
            'machine_id' => ['nullable', 'exists:manufacturing_machines,id'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
