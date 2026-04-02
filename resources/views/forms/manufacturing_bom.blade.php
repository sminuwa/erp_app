<form action="{{ isset($route) ? $route : route('manufacturing.boms.store') }}" method="POST" id="bom-form">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">BOM Header</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @if(isset($model->id))
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="reference">Reference</label>
                        <input type="text" class="form-control" value="{{ $model->reference }}" readonly>
                        <input type="hidden" name="reference" value="{{ $model->reference }}">
                    </div>
                </div>
                @else
                <input type="hidden" name="reference" value="{{ $model->reference }}">
                @endif
                <div class="col-md-{{ isset($model->id) ? '3' : '4' }}">
                    <div class="form-group">
                        <label for="bom_type">BOM Type</label>
                        <select class="form-control {{ $errors->has('bom_type') ? ' is-invalid' : '' }}"
                            name="bom_type" id="bom_type" required="required">
                            <option value="batch" {{ old('bom_type', $model->bom_type) == 'batch' ? 'selected' : '' }}>Batch Manufacturing</option>
                            <option value="single" {{ old('bom_type', $model->bom_type) == 'single' ? 'selected' : '' }}>Single Product</option>
                        </select>
                        @if ($errors->has('bom_type'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('bom_type') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-{{ isset($model->id) ? '6' : '8' }}">
                    <div class="form-group">
                        <label for="approval_document_no">Approval Document No (Memo)</label>
                        <input type="text" class="form-control {{ $errors->has('approval_document_no') ? ' is-invalid' : '' }}"
                            name="approval_document_no" id="approval_document_no" value="{{ old('approval_document_no', $model->approval_document_no) }}"
                            placeholder="Memo Reference" maxlength="100">
                        @if ($errors->has('approval_document_no'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('approval_document_no') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
                            name="description" id="description" value="{{ old('description', $model->description) }}"
                            placeholder="e.g., BOM for Best Choice Emulsion Paint 3040 Drum" maxlength="500" required="required">
                        @if ($errors->has('description'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('description') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="finish_product_id">Finish Product</label>
                        <select class="form-control select2-single {{ $errors->has('finish_product_id') ? ' is-invalid' : '' }}"
                            name="finish_product_id" id="finish_product_id" required="required">
                            <option value="">Select Finish Product...</option>
                            @if (isset($products))
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('finish_product_id', $model->finish_product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->code }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('finish_product_id'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('finish_product_id') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="output_store_id">Output Store</label>
                        <select class="form-control select2-single {{ $errors->has('output_store_id') ? ' is-invalid' : '' }}"
                            name="output_store_id" id="output_store_id" required="required">
                            <option value="">Select Output Store...</option>
                            @if (isset($stores))
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('output_store_id', $model->output_store_id) == $store->id ? 'selected' : '' }}>
                                        {{ $store->code }} - {{ $store->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('output_store_id'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('output_store_id') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="actual_output">Actual Output Qty</label>
                        <input type="number" step="0.0001" class="form-control {{ $errors->has('actual_output') ? ' is-invalid' : '' }}"
                            name="actual_output" id="actual_output" value="{{ old('actual_output', $model->actual_output ?? 1) }}"
                            placeholder="Output quantity per batch" min="0.0001" required="required">
                        @if ($errors->has('actual_output'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('actual_output') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row batch-fields" id="batch-fields">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="main_raw_material_id">Main Raw Material (Output Determinant)</label>
                        <select class="form-control select2-single {{ $errors->has('main_raw_material_id') ? ' is-invalid' : '' }}"
                            name="main_raw_material_id" id="main_raw_material_id">
                            <option value="">Select Main Raw Material...</option>
                            @if (isset($products))
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ old('main_raw_material_id', $model->main_raw_material_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->code }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('main_raw_material_id'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('main_raw_material_id') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="accepted_excess">Accepted Excess</label>
                        <input type="number" step="0.0001" class="form-control {{ $errors->has('accepted_excess') ? ' is-invalid' : '' }}"
                            name="accepted_excess" id="accepted_excess" value="{{ old('accepted_excess', $model->accepted_excess ?? 0) }}"
                            placeholder="0.0000" min="0">
                        @if ($errors->has('accepted_excess'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('accepted_excess') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="accepted_shortage">Accepted Shortage</label>
                        <input type="number" step="0.0001" class="form-control {{ $errors->has('accepted_shortage') ? ' is-invalid' : '' }}"
                            name="accepted_shortage" id="accepted_shortage" value="{{ old('accepted_shortage', $model->accepted_shortage ?? 0) }}"
                            placeholder="0.0000" min="0">
                        @if ($errors->has('accepted_shortage'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('accepted_shortage') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="margin_per_piece">Margin per Piece <small class="text-muted">(optional)</small></label>
                        <input type="number" step="0.0001" class="form-control {{ $errors->has('margin_per_piece') ? ' is-invalid' : '' }}"
                            name="margin_per_piece" id="margin_per_piece" value="{{ old('margin_per_piece', $model->margin_per_piece) }}"
                            placeholder="0.0000" min="0">
                        @if ($errors->has('margin_per_piece'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('margin_per_piece') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="margin_gl_account_id">Margin GL Account <small class="text-muted">(optional)</small></label>
                        <select class="form-control select2-single {{ $errors->has('margin_gl_account_id') ? ' is-invalid' : '' }}"
                            name="margin_gl_account_id" id="margin_gl_account_id">
                            <option value="">Select GL Account...</option>
                            @if (isset($glAccounts))
                                @foreach ($glAccounts as $glAccount)
                                    <option value="{{ $glAccount->id }}" {{ old('margin_gl_account_id', $model->margin_gl_account_id) == $glAccount->id ? 'selected' : '' }}>
                                        {{ $glAccount->number }} - {{ $glAccount->description }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('margin_gl_account_id'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('margin_gl_account_id') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="1" name="status" id="status_yes"
                        {{ old('status', $model->status) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="status_yes">Active</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input class="form-check-input" type="radio" value="0" name="status" id="status_no"
                        {{ old('status', $model->status) === 0 || old('status', $model->status) === '0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="status_no">Inactive</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="bomTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="materials-tab" data-toggle="tab" href="#materials" role="tab">
                        Materials
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="costs-tab" data-toggle="tab" href="#costs" role="tab">
                        Other Costs
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="bomTabsContent">
                <div class="tab-pane fade show active" id="materials" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="materials-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="30%">Raw Product</th>
                                    <th width="20%">Source Store</th>
                                    <th width="12%">Quantity</th>
                                    <th width="12%">Unit Cost</th>
                                    <th width="15%">Total Cost</th>
                                    <th width="6%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="materials-body">
                                @php
                                    $existingMaterials = old('materials', isset($model->id) ? $model->materials->toArray() : []);
                                @endphp
                                @if(count($existingMaterials) > 0)
                                    @foreach($existingMaterials as $index => $material)
                                        <tr class="material-row" data-row="{{ $index }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <select class="form-control select2-single material-product" name="materials[{{ $index }}][product_id]" required>
                                                    <option value="">Select Product...</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" {{ ($material['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                                            {{ $product->code }} - {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control select2-single material-store" name="materials[{{ $index }}][source_store_id]">
                                                    <option value="">Select Store...</option>
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}" {{ ($material['source_store_id'] ?? '') == $store->id ? 'selected' : '' }}>
                                                            {{ $store->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.0001" class="form-control material-qty" name="materials[{{ $index }}][quantity]" value="{{ $material['quantity'] ?? '' }}" min="0.0001" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control material-unit-cost" value="{{ number_format($material['unit_cost'] ?? 0, 2, '.', '') }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control material-total-cost" value="{{ number_format($material['total_cost'] ?? 0, 2, '.', '') }}" readonly>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove-material"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="material-row" data-row="0">
                                        <td>1</td>
                                        <td>
                                            <select class="form-control select2-single material-product" name="materials[0][product_id]" required>
                                                <option value="">Select Product...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->code }} - {{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control select2-single material-store" name="materials[0][source_store_id]">
                                                <option value="">Select Store...</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" class="form-control material-qty" name="materials[0][quantity]" min="0.0001" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control material-unit-cost" value="0.00" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control material-total-cost" value="0.00" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm remove-material"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right"><strong>Total Material Cost:</strong></td>
                                    <td><strong id="total-material-cost">0.00</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" id="add-material">
                        <i class="fa fa-plus"></i> Add Material
                    </button>
                    <button type="button" class="btn btn-info btn-sm ml-2" id="refresh-costs">
                        <i class="fa fa-sync"></i> Refresh Costs
                    </button>
                </div>

                <div class="tab-pane fade" id="costs" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="labor_cost">Labor Cost</label>
                                <input type="number" step="0.01" class="form-control other-cost {{ $errors->has('labor_cost') ? ' is-invalid' : '' }}"
                                    name="labor_cost" id="labor_cost" value="{{ old('labor_cost', $model->labor_cost ?? 0) }}"
                                    placeholder="0.00" min="0">
                                @if ($errors->has('labor_cost'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('labor_cost') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="power_cost">Power Cost</label>
                                <input type="number" step="0.01" class="form-control other-cost {{ $errors->has('power_cost') ? ' is-invalid' : '' }}"
                                    name="power_cost" id="power_cost" value="{{ old('power_cost', $model->power_cost ?? 0) }}"
                                    placeholder="0.00" min="0">
                                @if ($errors->has('power_cost'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('power_cost') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="other_cost">Other Costs</label>
                                <input type="number" step="0.01" class="form-control other-cost {{ $errors->has('other_cost') ? ' is-invalid' : '' }}"
                                    name="other_cost" id="other_cost" value="{{ old('other_cost', $model->other_cost ?? 0) }}"
                                    placeholder="0.00" min="0">
                                @if ($errors->has('other_cost'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('other_cost') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Other Cost:</th>
                                    <td class="text-right cost-display" id="total-other-cost">0.00</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Material Cost:</th>
                            <td class="text-right cost-display" id="summary-material-cost">0.00</td>
                        </tr>
                        <tr>
                            <th>Total Other Cost:</th>
                            <td class="text-right cost-display" id="summary-other-cost">0.00</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Total Cost:</th>
                            <td class="text-right cost-display" id="summary-total-cost">0.00</td>
                        </tr>
                        <tr class="table-success">
                            <th>Cost Per Unit:</th>
                            <td class="text-right cost-display" id="summary-unit-cost">0.00</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Save BOM
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
