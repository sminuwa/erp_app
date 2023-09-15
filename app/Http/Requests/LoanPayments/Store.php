<?php

namespace App\Http\Requests\LoanPayments;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\LoanPayment;

class Store extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('make.loan.payment', LoanPayment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'loan_id' => 'required|numeric',
			'amount' => 'nullable|numeric',
			'payment_mode' => 'required|in:Cash,Cheque',
			'bank_account_id' => 'nullable|numeric',
			'cheque_no' => 'nullable|max:20|unique:loan_payments,cheque_no',
			'receipt_no' => 'required|max:20',
			'received_by' => 'required|numeric',
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
