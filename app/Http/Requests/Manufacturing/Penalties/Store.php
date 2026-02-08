<?php

namespace App\Http\Requests\Manufacturing\Penalties;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.penalties.create');
    }

    public function rules()
    {
        return [
            'reference' => ['required', 'unique:manufacturing_penalties,reference'],
            'penalty_date' => ['required', 'date'],
            'penalty_type' => ['required', 'in:team,staff'],
            'team_id' => ['nullable', 'exists:manufacturing_teams,id'],
            'staff_id' => ['nullable', 'exists:manufacturing_staff,id'],
            'amount_charged' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string'],
        ];
    }
}
