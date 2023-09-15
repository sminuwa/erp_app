<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Purchase;
class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('edit.item.purchase', Purchase::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'supplier_id' => 'required|numeric',
			'invoice' => 'required|max:191',
			'purchase_date' => 'required|date',
			'purchase_mode' => 'required|in:Cash,Credit,Cash/Credit',
			'vehicle_reg_no' => 'nullable|max:191',
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
