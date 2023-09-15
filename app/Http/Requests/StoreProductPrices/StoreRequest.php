<?php

namespace App\Http\Requests\StoreProductPrices;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\StoreProductPrice;

class StoreRequest extends FormRequest 
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() 
    {
        return $this->user()->can('set.product.price', StoreProductPrice::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() 
    {
        return [
			'store_id' => 'required',
			'product_id' => 'required|numeric',
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
