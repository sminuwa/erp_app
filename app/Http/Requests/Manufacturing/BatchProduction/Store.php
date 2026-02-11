<?php

namespace App\Http\Requests\Manufacturing\BatchProduction;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.batch_production.create');
    }

    public function rules()
    {
        return [
            'reference' => ['required', 'unique:batch_productions,reference'],
            'batch_number' => ['required', 'string'],
            'production_date' => ['required', 'date'],
            'requisition_id' => ['required', 'exists:materials_requisitions,id'],
            'team_id' => ['required', 'exists:manufacturing_teams,id'],
            'bom_id' => ['required', 'exists:manufacturing_boms,id'],
            'machine_id' => ['nullable', 'exists:manufacturing_machines,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
