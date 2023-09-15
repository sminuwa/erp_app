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
			'generic_name' => 'required|max:191',
			'strength' => 'required|max:191',
			'category_id' => 'required|numeric',
			'company_id' => 'required|numeric',
			'dosage_form_id' => 'required|numeric',
            'barcode' => 'required|max:191|unique:products,barcode,{$this->product->id}',
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
