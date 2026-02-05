<?php

namespace App\Http\Requests\Manufacturing\Machines;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('manufacturing.machines.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|max:20|unique:manufacturing_machines,code',
            'description' => 'required|max:255',
            'capacity' => 'nullable|numeric|min:0',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:0,1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => 'The machine code is required.',
            'code.unique' => 'This machine code already exists.',
            'description.required' => 'The description is required.',
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch is invalid.',
        ];
    }
}
