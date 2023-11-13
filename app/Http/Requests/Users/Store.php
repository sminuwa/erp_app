<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class Store extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create.user', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'name' => 'required|max:191',
			'email' => 'required|unique:users,email|max:100',
			'phone' => 'required|max:15',
			'gender' => 'required|in:Male,Female',
			'user_code' => 'required|unique:users,user_code|max:15',
			'branch_id' => 'required|numeric',
			'status' => 'required|boolean',
        ];
    }

    /**
    * Get the error messages for the defined validation rules.
    *
    * @return array
    */
    public function messages()
    {
        return [

        ];
    }

}
