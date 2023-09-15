<?php

namespace App\Http\Requests\Employees;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Employee;

class Store extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('create.staff', Employee::class);
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
			'email' => 'required|max:191',
			'phone' => 'required|max:191',
			'address' => 'required|max:191',
			'experience' => 'required|max:191',
			'photo' => 'required|max:191',
			'salary' => 'required|max:191',
			'vacation' => 'required|max:191',
			'city' => 'required|max:191',
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
