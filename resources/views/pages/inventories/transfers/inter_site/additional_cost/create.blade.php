@extends('layouts.backend.app')

@section('title', isset($model->id) ? 'Edit Additional Cost' : 'Create Additional Cost')

@push('css')
<style>
    .intersite-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .intersite-info .row {
        margin-bottom: 8px;
    }
    .intersite-info label {
        font-weight: 600;
    }
    .intersite-products-table {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>{{ isset($model->id) ? 'Edit' : 'Create' }} Intersite Additional Cost</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('intersite.additional_cost.index') }}">Intersite Additional Costs</a></li>
                        <li class="breadcrumb-item active">{{ isset($model->id) ? 'Edit' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="{{ isset($model->id) ? route('intersite.additional_cost.update', $model->id) : route('intersite.additional_cost.store') }}" method="POST">
            @csrf
            @if(isset($model->id))
                @method('PUT')
            @endif
            <input type="hidden" name="reference" value="{{ $model->reference }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Intersite Additional Cost Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Reference</label>
                                        <input type="text" class="form-control" value="{{ $model->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date <span class="text-danger">*</span></label>
                                        <input type="date" name="date" class="form-control" value="{{ old('date', $model->date) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cost Mode <span class="text-danger">*</span></label>
                                        <select name="cost_mode" id="cost_mode" class="form-control" required>
                                            <option value="by_amount" {{ old('cost_mode', $model->cost_mode ?? 'by_amount') == 'by_amount' ? 'selected' : '' }}>By Amount</option>
                                            <option value="by_qty" {{ old('cost_mode', $model->cost_mode ?? '') == 'by_qty' ? 'selected' : '' }}>By Quantity</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount', $model->amount) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Intersite Transfer <span class="text-danger">*</span></label>
                                        <select name="intersite_transfer_id" id="intersite_transfer_id" class="form-control select2-single" required>
                                            <option value="">Search intersite transfer...</option>
                                            @foreach($intersites as $intersite)
                                                <option value="{{ $intersite->id }}"
                                                    {{ old('intersite_transfer_id', $model->intersite_transfer_id ?? '') == $intersite->id ? 'selected' : '' }}>
                                                    {{ $intersite->reference }} - From: {{ $intersite->source->name ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Supplier <span class="text-danger">*</span></label>
                                        <select name="supplier_id" id="supplier_id" class="form-control select2-single" required>
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ old('supplier_id', $model->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->code }} - {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Truck Number</label>
                                        <input type="text" name="truck_number" class="form-control" placeholder="Enter truck number" value="{{ old('truck_number', $model->truck_number) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Waybill No</label>
                                        <input type="text" name="waybill_no" class="form-control" placeholder="Enter waybill number" value="{{ old('waybill_no', $model->waybill_no) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="1" placeholder="Enter description">{{ old('description', $model->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Intersite Info Panel -->
                            <div class="row" id="intersite-info-panel" style="display: none;">
                                <div class="col-md-12">
                                    <div class="intersite-info">
                                        <h6 class="mb-3"><i class="fa fa-info-circle"></i> Selected Intersite Details</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Reference:</label>
                                                <div id="info-reference">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Source Branch:</label>
                                                <div id="info-source">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Product Count:</label>
                                                <div id="info-product-count">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Total Value:</label>
                                                <div id="info-total-value">-</div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label>Products:</label>
                                                <div class="intersite-products-table">
                                                    <table class="table table-sm table-bordered" id="info-products-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Code</th>
                                                                <th>Name</th>
                                                                <th>Quantity</th>
                                                                <th>Unit Cost</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Note:</strong> Additional costs are applied to posted intersite transfers.
                                The cost will be distributed across the transferred products and will affect the product cost upon receiving.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> {{ isset($model->id) ? 'Update' : 'Save' }} Additional Cost
                            </button>
                            <a href="{{ route('intersite.additional_cost.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#intersite_transfer_id').select2({ width: '100%', placeholder: 'Search intersite transfer...' });
    $('#supplier_id').select2({ width: '100%', placeholder: 'Select Supplier' });

    function loadIntersiteDetails(intersiteId) {
        if (!intersiteId) {
            $('#intersite-info-panel').hide();
            return;
        }

        $.ajax({
            url: '{{ route("intersite.additional_cost.intersite_details", ":id") }}'.replace(':id', intersiteId),
            type: 'GET',
            success: function(data) {
                $('#info-reference').text(data.reference);
                $('#info-source').text(data.source);
                $('#info-product-count').text(data.product_count);
                $('#info-total-value').text('₦' + data.total_value);

                var tbody = $('#info-products-table tbody');
                tbody.empty();
                $.each(data.products, function(i, product) {
                    tbody.append(
                        '<tr>' +
                        '<td>' + product.code + '</td>' +
                        '<td>' + product.name + '</td>' +
                        '<td>' + product.quantity + '</td>' +
                        '<td>₦' + product.cost_price + '</td>' +
                        '<td>₦' + product.total + '</td>' +
                        '</tr>'
                    );
                });

                $('#intersite-info-panel').show();
            },
            error: function() {
                $('#intersite-info-panel').hide();
            }
        });
    }

    // Load details when intersite is selected
    $('#intersite_transfer_id').on('change', function() {
        loadIntersiteDetails($(this).val());
    });

    // Load details on page load if editing
    @if(isset($model->id) && $model->intersite_transfer_id)
        loadIntersiteDetails('{{ $model->intersite_transfer_id }}');
    @elseif(old('intersite_transfer_id'))
        loadIntersiteDetails('{{ old("intersite_transfer_id") }}');
    @endif
});
</script>
@endpush
