<?php

namespace App\Http\Requests\LoanCollectors;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\LoanCollector;

class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('edit.loan.collector', LoanCollector::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'name' => 'required|max:50',
			'address' => 'nullable|max:50',
			'email' => 'nullable|max:50',
			'phone' => 'required|max:15',
			'reg_code' => 'required|max:10',
			'registered_by' => 'required|numeric',
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
