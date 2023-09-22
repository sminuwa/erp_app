<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class Store extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('create.product', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'name' => 'required|max:191|unique:products,name',
			'code' => 'required|max:30|unique:products,column,code',
			'category_id' => 'required|numeric|exists:categories,id',
            'barcode' => 'required|max:191|unique:products,barcode',
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
