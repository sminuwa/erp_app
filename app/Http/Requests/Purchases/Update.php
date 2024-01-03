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
<<<<<<< HEAD
        return $this->user()->can('purchases.edit');
=======
        return $this->user()->can('purchases.update');
>>>>>>> 9bbd79a83914354ccd77f4f5a4c86e3f1878d3f0
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
			'purchase_date' => 'nullable|date',
			'vehicle_reg_no' => 'nullable|max:191',
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
