<?php

namespace App\Http\Requests\Manufacturing\Boms;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('manufacturing.boms.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $bomId = $this->route('bom')->id;

        return [
            'bom_type' => 'required|in:batch,single',
            'description' => 'required|max:500',
            'finish_product_id' => 'required|exists:products,id',
            'output_store_id' => 'required|exists:stores,id',
            'actual_output' => 'required|numeric|min:0.0001',
            'accepted_excess' => 'nullable|numeric|min:0',
            'accepted_shortage' => 'nullable|numeric|min:0',
            'main_raw_material_id' => 'nullable|exists:products,id',
            'labor_cost' => 'nullable|numeric|min:0',
            'power_cost' => 'nullable|numeric|min:0',
            'other_cost' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'approval_document_no' => 'nullable|max:100',
            'status' => 'required|in:0,1',
            'materials' => 'required|array|min:1',
            'materials.*.product_id' => 'required|exists:products,id',
            'materials.*.quantity' => 'required|numeric|min:0.0001',
            'materials.*.source_store_id' => 'nullable|exists:stores,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'bom_type.required' => 'Please select a BOM type.',
            'description.required' => 'The description is required.',
            'finish_product_id.required' => 'Please select a finish product.',
            'output_store_id.required' => 'Please select an output store.',
            'actual_output.required' => 'The actual output quantity is required.',
            'actual_output.min' => 'The actual output must be greater than zero.',
            'materials.required' => 'At least one material is required.',
            'materials.min' => 'At least one material is required.',
        ];
    }
}
