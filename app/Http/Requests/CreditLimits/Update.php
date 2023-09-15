<?php

namespace App\Http\Requests\CreditLimits;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CreditLimit;

class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('edit.customer.credit.limit', CreditLimit::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'customer_id' => 'required|numeric',
			'amount' => 'required|numeric',
			'status' => 'required',
			'updated_by' => 'required|numeric',
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
