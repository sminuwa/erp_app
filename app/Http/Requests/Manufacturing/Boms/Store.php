<?php

namespace App\Http\Requests\Manufacturing\Boms;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('manufacturing.boms.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reference' => 'required|max:50|unique:manufacturing_boms,reference',
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
            'reference.required' => 'The reference number is required.',
            'reference.unique' => 'This reference number already exists.',
            'bom_type.required' => 'Please select a BOM type.',
            'description.required' => 'The description is required.',
            'finish_product_id.required' => 'Please select a finish product.',
            'finish_product_id.exists' => 'The selected finish product is invalid.',
            'output_store_id.required' => 'Please select an output store.',
            'output_store_id.exists' => 'The selected output store is invalid.',
            'actual_output.required' => 'The actual output quantity is required.',
            'actual_output.min' => 'The actual output must be greater than zero.',
            'materials.required' => 'At least one material is required.',
            'materials.min' => 'At least one material is required.',
            'materials.*.product_id.required' => 'Each material must have a product selected.',
            'materials.*.quantity.required' => 'Each material must have a quantity.',
        ];
    }
}
