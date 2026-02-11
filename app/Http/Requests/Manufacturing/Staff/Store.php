<?php

namespace App\Http\Requests\Manufacturing\Staff;

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
        return $this->user()->can('manufacturing.staff.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employment_id' => 'required|max:50|unique:manufacturing_staff,employment_id',
            'name' => 'required|max:191',
            'phone_number' => 'nullable|max:50',
            'address' => 'nullable|max:500',
            'bvn' => 'nullable|max:20',
            'nin' => 'nullable|max:20',
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
            'employment_id.required' => 'The employment ID is required.',
            'employment_id.unique' => 'This employment ID already exists.',
            'name.required' => 'The staff name is required.',
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch is invalid.',
        ];
    }
}
