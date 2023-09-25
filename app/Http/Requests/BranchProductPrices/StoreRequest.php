<?php

namespace App\Http\Requests\BranchProductPrices;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\BranchProductPrice;

class StoreRequest extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('set.product.price', BranchProductPrice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'branch_id' => 'required|exists:branches,id',
			'product_id' => 'required|numeric|exists:products,id',
			'selling_price' => 'required|numeric',
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
