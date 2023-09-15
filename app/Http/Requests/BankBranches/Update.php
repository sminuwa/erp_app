<?php

namespace App\Http\Requests\BankBranches;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BankBranch;

class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('edit.bank.branch', BankBranch::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'name' => 'required|unique:bank_branches,name|max:100',
			'sortcode' => 'nullable|max:191',
			'bank_id' => 'required|numeric|exists:banks,id',
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
            'bank_id.exists'=>'Invalid bank',
        ];
    }

}
