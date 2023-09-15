<?php

namespace App\Http\Requests\CashMovements;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CashMovement;
class Store extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('make.deposit.withdraw', CashMovement::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'amount' => 'required|numeric',
			'from_account' => 'required|numeric',
			'to_account' => 'required|numeric',
			'sent_by' => 'required|numeric',
			'date_withdraw' => 'required|date',
            'date_deposit' => 'required|date',
            'slip_no'=>'required|unique:cash_movements,slip_no'
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
