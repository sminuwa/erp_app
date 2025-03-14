<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Customer;

class Update extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('customers.edit', Customer::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|max:50',
            'email' => 'nullable|max:191',
            'phone' => 'required|max:191',
            'address' => 'required|max:191',
            'credit_limit' => 'nullable|numeric',
            'relation_officer' => 'required',
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
