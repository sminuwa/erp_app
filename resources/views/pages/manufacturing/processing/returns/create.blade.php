@extends('layouts.backend.app')

@section('title', 'Create Manufacturing Return')

@push('css')
<style>
    .production-info { display: none; }
    .production-info.active { display: block; }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Manufacturing Return</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Return</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="{{ route('manufacturing.returns.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="{{ $model->reference }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Manufacturing Return Details</h3>
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
                                        <label>Return Date <span class="text-danger">*</span></label>
                                        <input type="date" name="return_date" class="form-control" value="{{ $model->return_date }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Production Document <span class="text-danger">*</span></label>
                                        <select name="production_select" id="production_select" class="form-control select2-single" required>
                                            <option value="">Select Production</option>
                                            <optgroup label="Single Product Manufacturing">
                                                @foreach($singleManufacturing as $sm)
                                                <option value="single_{{ $sm->id }}"
                                                    data-type="single_product"
                                                    data-id="{{ $sm->id }}"
                                                    data-product="{{ $sm->bom->finishProduct->name ?? 'N/A' }}"
                                                    data-qty="{{ $sm->quantity }}"
                                                    data-returned="{{ $sm->getTotalReturnedQty() }}"
                                                    data-remaining="{{ $sm->getRemainingReturnableQty() }}">
                                                    {{ $sm->reference }} - {{ $sm->bom->finishProduct->name ?? 'N/A' }} (Remaining: {{ number_format($sm->getRemainingReturnableQty(), 4) }})
                                                </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Batch Conversion">
                                                @foreach($batchConversions as $bc)
                                                <option value="batch_{{ $bc->id }}"
                                                    data-type="batch_conversion"
                                                    data-id="{{ $bc->id }}"
                                                    data-product="{{ $bc->finishProduct->name ?? ($bc->batchProduction->bom->finishProduct->name ?? 'N/A') }}"
                                                    data-qty="{{ $bc->produced_qty }}"
                                                    data-returned="{{ $bc->getTotalReturnedQty() }}"
                                                    data-remaining="{{ $bc->getRemainingReturnableQty() }}">
                                                    {{ $bc->reference }} - {{ $bc->finishProduct->name ?? ($bc->batchProduction->bom->finishProduct->name ?? 'N/A') }} (Remaining: {{ number_format($bc->getRemainingReturnableQty(), 4) }})
                                                </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <input type="hidden" name="single_manufacturing_id" id="single_manufacturing_id" value="">
                                        <input type="hidden" name="batch_conversion_id" id="batch_conversion_id" value="">
                                    </div>
                                </div>
                            </div>

                            <!-- Production Info Panel -->
                            <div class="row production-info" id="production-info-panel">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Finish Product:</strong>
                                                <span id="info-product">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Produced Qty:</strong>
                                                <span id="info-qty">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Already Returned:</strong>
                                                <span id="info-returned">-</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Returnable Qty:</strong>
                                                <span id="info-remaining" class="text-white font-weight-bold">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Return Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="return_qty" id="return_qty" class="form-control" step="0.0001" min="0.0001" required>
                                        <small class="text-muted">Max returnable: <span id="max-returnable">0</span></small>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Reason <span class="text-danger">*</span></label>
                                        <textarea name="reason" class="form-control" rows="2" required maxlength="500" placeholder="Enter return reason">{{ old('reason') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Raw Materials Preview -->
                            <div id="materials-preview" style="display:none;">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fa fa-cubes"></i> Raw Materials to be Returned</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-striped table-sm mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Store</th>
                                                    <th class="text-right">Original Qty</th>
                                                    <th class="text-right">Return Qty</th>
                                                    <th class="text-right">Unit Cost</th>
                                                    <th class="text-right">Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody id="materials-tbody"></tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th colspan="6" class="text-right">Total Materials Cost:</th>
                                                    <th class="text-right" id="materials-total-cost">0.00</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fa fa-info-circle"></i>
                                <strong>Note:</strong> Upon posting, the return will:
                                <ul class="mb-0 mt-1">
                                    <li>Credit back raw materials to inventory (as shown above)</li>
                                    <li>Debit finish goods from inventory</li>
                                    <li>Recalculate average cost for affected products</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Return
                            </button>
                            <a href="{{ route('manufacturing.returns.index') }}" class="btn btn-secondary">Cancel</a>
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
    $('.select2-single').select2({ width: '100%' });

    $('#production_select').on('change', function() {
        var selected = $(this).find('option:selected');
        var type = selected.data('type');
        var id = selected.data('id');

        // Reset hidden fields
        $('#single_manufacturing_id').val('');
        $('#batch_conversion_id').val('');

        if (type && id) {
            // Set the appropriate hidden field
            if (type === 'single_product') {
                $('#single_manufacturing_id').val(id);
            } else if (type === 'batch_conversion') {
                $('#batch_conversion_id').val(id);
            }

            // Update info panel
            $('#info-product').text(selected.data('product'));
            $('#info-qty').text(parseFloat(selected.data('qty')).toFixed(4));
            $('#info-returned').text(parseFloat(selected.data('returned')).toFixed(4));
            $('#info-remaining').text(parseFloat(selected.data('remaining')).toFixed(4));
            $('#max-returnable').text(parseFloat(selected.data('remaining')).toFixed(4));

            // Update max on return qty input
            $('#return_qty').attr('max', selected.data('remaining'));

            // Show info panel
            $('#production-info-panel').addClass('active');
        } else {
            $('#production-info-panel').removeClass('active');
            $('#max-returnable').text('0');
            $('#return_qty').removeAttr('max');
        }
    });

    // Production materials data (keyed by "single_{id}" or "batch_{id}")
    var productionMaterials = {
        @foreach($singleManufacturing as $sm)
        'single_{{ $sm->id }}': {
            production_qty: {{ $sm->quantity }},
            materials: [
                @foreach($sm->materials as $mat)
                { product: '{{ addslashes($mat->product->name ?? "N/A") }}', store: '{{ addslashes($mat->store->name ?? "N/A") }}', quantity: {{ $mat->quantity }}, unit_cost: {{ $mat->unit_cost }} },
                @endforeach
            ]
        },
        @endforeach
        @foreach($batchConversions as $bc)
        @if($bc->batchProduction)
        'batch_{{ $bc->id }}': {
            production_qty: {{ $bc->batchProduction->quantity }},
            materials: [
                @foreach($bc->batchProduction->materials as $mat)
                { product: '{{ addslashes($mat->product->name ?? "N/A") }}', store: '{{ addslashes($mat->store->name ?? "N/A") }}', quantity: {{ $mat->quantity }}, unit_cost: {{ $mat->unit_cost }} },
                @endforeach
            ]
        },
        @endif
        @endforeach
    };

    function updateMaterialsPreview() {
        var selectVal = $('#production_select').val();
        var returnQty = parseFloat($('#return_qty').val()) || 0;

        if (!selectVal || returnQty <= 0 || !productionMaterials[selectVal]) {
            $('#materials-preview').hide();
            return;
        }

        var data = productionMaterials[selectVal];
        var ratio = returnQty / data.production_qty;
        var tbody = '';
        var totalCost = 0;

        for (var i = 0; i < data.materials.length; i++) {
            var m = data.materials[i];
            var returnMaterialQty = (m.quantity * ratio);
            var lineCost = returnMaterialQty * m.unit_cost;
            totalCost += lineCost;

            tbody += '<tr>' +
                '<td>' + (i + 1) + '</td>' +
                '<td>' + m.product + '</td>' +
                '<td>' + m.store + '</td>' +
                '<td class="text-right">' + m.quantity.toFixed(4) + '</td>' +
                '<td class="text-right">' + returnMaterialQty.toFixed(4) + '</td>' +
                '<td class="text-right">' + m.unit_cost.toFixed(2) + '</td>' +
                '<td class="text-right">' + lineCost.toFixed(2) + '</td>' +
                '</tr>';
        }

        $('#materials-tbody').html(tbody);
        $('#materials-total-cost').text(totalCost.toFixed(2));
        $('#materials-preview').show();
    }

    // Update materials preview when production or return qty changes
    $('#production_select').on('change', updateMaterialsPreview);
    $('#return_qty').on('input change', updateMaterialsPreview);

    // Validate return quantity doesn't exceed remaining
    $('form').on('submit', function(e) {
        var returnQty = parseFloat($('#return_qty').val()) || 0;
        var maxReturnable = parseFloat($('#production_select option:selected').data('remaining')) || 0;

        if (returnQty > maxReturnable) {
            e.preventDefault();
            alert('Return quantity cannot exceed the remaining returnable quantity (' + maxReturnable.toFixed(4) + ')');
            return false;
        }
    });
});
</script>
@endpush
