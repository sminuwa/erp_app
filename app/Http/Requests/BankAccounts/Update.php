<?php

namespace App\Http\Requests\BankAccounts;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BankAccount;
class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('edit.bank.account', BankAccount::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'account_name' => 'required|max:100',
			'account_no' => 'nullable|max:191',
			'branch_id' => 'required|numeric',
			'account_balance' => 'required|numeric',
			'account_type' => 'required|in:Current,Savings,Credit,Domiciliary,Cash',
			'status' => 'required',
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
