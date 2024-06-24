<?php

namespace App\Http\Requests\PurchaseRequest;

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
        return $this->user()->can('purchases.request.update');
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
			'wbno' => 'nullable|max:191',
			'status' => 'required',
			'waybill_no' => 'nullable|max:50',
			'driver_name' => 'nullable|max:100',
			'location_id' => 'nullable|max:100',
			'warehouse' => 'nullable|max:100',
			'vehicle_reg_no' => 'nullable|max:20',
			'transporter' => 'nullable|max:100',
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
