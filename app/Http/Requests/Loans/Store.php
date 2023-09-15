<?php

namespace App\Http\Requests\Loans;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Loan;

class Store extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('issue.loan', Loan::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'loan_collector_id' => 'required|numeric',
			'amount' => 'required|numeric',
			'payment_mode' => 'required|in:Cash,Cheque',
			'bank_account_id' => 'nullable|numeric',
			'date' => 'required|date',
			'granted_by' => 'required|numeric',
			'receipt_no' => 'required|max:20',
			'due_date' => 'required|date',
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
