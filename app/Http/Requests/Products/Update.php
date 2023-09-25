<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class Update extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('edit.product', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'name' => "required|max:191|unique:products,name,{$this->product->id}",
			'code' => 'required|max:191',
			'category_id' => 'required|numeric|exists:categories,id',
            'barcode' => "required|max:191|unique:products,barcode,{$this->product->id}",
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
