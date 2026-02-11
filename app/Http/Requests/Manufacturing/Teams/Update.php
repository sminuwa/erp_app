<?php

namespace App\Http\Requests\Manufacturing\Teams;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('manufacturing.teams.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $teamId = $this->route('team')->id;

        return [
            'name' => 'required|max:191|unique:manufacturing_teams,name,' . $teamId,
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:0,1',
            'supervisors' => 'nullable|array',
            'supervisors.*' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'nullable|exists:manufacturing_staff,id',
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
            'name.required' => 'The team name is required.',
            'name.unique' => 'This team name already exists.',
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch is invalid.',
        ];
    }
}
