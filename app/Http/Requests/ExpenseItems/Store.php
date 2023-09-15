<?php

namespace App\Http\Requests\ExpenseItems;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ExpenseItem;

class Store extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('create.expense.item', ExpenseItem::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'name' => 'required|unique:expense_items,name|max:100',
			'code' => 'required|unique:expense_items,code|max:10',
			'created_by' => 'required|numeric',
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
