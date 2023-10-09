<?php

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Purchase;

class StoreRequest extends FormRequest

{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('make.item.purchase', Purchase::class);
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
            'vehicle_reg_no' => 'nullable|max:191',
            'source_store_id' => 'required|numeric',
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
