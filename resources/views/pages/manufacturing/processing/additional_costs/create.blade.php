@extends('layouts.backend.app')

@section('title', 'Create Additional Cost')

@push('css')
<style>
    .production-select { display: none; }
    .production-select.active { display: block; }
    .production-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .production-info .row {
        margin-bottom: 8px;
    }
    .production-info label {
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Create Additional Cost</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Manufacturing</li>
                        <li class="breadcrumb-item">Processing</li>
                        <li class="breadcrumb-item active">Create Additional Cost</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <form action="{{ route('manufacturing.additional_costs.store') }}" method="POST">
            @csrf
            <input type="hidden" name="reference" value="{{ $model->reference }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create Manufacturing Additional Cost</h3>
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
                                        <label>Cost Date <span class="text-danger">*</span></label>
                                        <input type="date" name="cost_date" class="form-control" value="{{ $model->cost_date }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Production Type <span class="text-danger">*</span></label>
                                        <select name="production_type" id="production_type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="single_product">Single Product Manufacturing</option>
                                            <option value="batch_conversion">Batch Conversion</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Account <span class="text-danger">*</span></label>
                                        <select name="account_id" class="form-control select2-single" required>
                                            <option value="">Select Account</option>
                                            @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->number }} - {{ $account->description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Single Product Manufacturing Select -->
                                <div class="col-md-4 production-select" id="single-select">
                                    <div class="form-group">
                                        <label>Single Product Manufacturing <span class="text-danger">*</span></label>
                                        <select name="single_manufacturing_id" id="single_manufacturing_id" class="form-control select2-single">
                                            <option value="">Select Production</option>
                                            @forelse($singleManufacturing as $sm)
                                            <option value="{{ $sm->id }}"
                                                data-reference="{{ $sm->reference }}"
                                                data-product="{{ $sm->bom->finishProduct->name ?? 'N/A' }}"
                                                data-quantity="{{ number_format($sm->quantity, 4) }}"
                                                data-total-cost="{{ number_format($sm->total_cost, 2) }}"
                                                data-batch="{{ $sm->batch_number }}">
                                                {{ $sm->reference }} - {{ $sm->bom->finishProduct->name ?? 'N/A' }} (Qty: {{ number_format($sm->quantity, 4) }})
                                            </option>
                                            @empty
                                            <option value="" disabled>No posted single product manufacturing found</option>
                                            @endforelse
                                        </select>
                                        @if(count($singleManufacturing) == 0)
                                        <small class="text-warning">No posted Single Product Manufacturing records available</small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Batch Conversion Select -->
                                <div class="col-md-4 production-select" id="batch-select">
                                    <div class="form-group">
                                        <label>Batch Conversion <span class="text-danger">*</span></label>
                                        <select name="batch_production_id" id="batch_production_id" class="form-control select2-single">
                                            <option value="">Select Conversion</option>
                                            @forelse($batchConversions as $bc)
                                            @php
                                                $bcFinishProduct = $bc->finishProduct ?? ($bc->batchProduction->bom->finishProduct ?? null);
                                            @endphp
                                            <option value="{{ $bc->id }}"
                                                data-reference="{{ $bc->reference }}"
                                                data-product="{{ $bcFinishProduct->name ?? 'N/A' }}"
                                                data-quantity="{{ number_format($bc->produced_qty, 4) }}"
                                                data-total-cost="{{ number_format($bc->total_cost, 2) }}"
                                                data-batch="{{ $bc->batchProduction->batch_number ?? '' }}">
                                                {{ $bc->reference }} - {{ $bcFinishProduct->name ?? 'N/A' }} (Qty: {{ number_format($bc->produced_qty, 4) }})
                                            </option>
                                            @empty
                                            <option value="" disabled>No posted batch conversion found</option>
                                            @endforelse
                                        </select>
                                        @if(count($batchConversions) == 0)
                                        <small class="text-warning">No posted Batch Conversion records available</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Amount <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Production Info Panel -->
                            <div class="row" id="production-info-panel" style="display: none;">
                                <div class="col-md-12">
                                    <div class="production-info">
                                        <h6 class="mb-3"><i class="fa fa-info-circle"></i> Selected Production Details</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Reference:</label>
                                                <div id="info-reference">-</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Finish Product:</label>
                                                <div id="info-product">-</div>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Quantity:</label>
                                                <div id="info-quantity">-</div>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Total Cost:</label>
                                                <div id="info-total-cost">-</div>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Batch Number:</label>
                                                <div id="info-batch">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="Enter description for this additional cost">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Note:</strong> Additional costs are applied to posted production records.
                                The cost will be distributed across the produced quantity and will update the product's weighted average cost upon posting.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Additional Cost
                            </button>
                            <a href="{{ route('manufacturing.additional_costs.index') }}" class="btn btn-secondary">Cancel</a>
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
    function reinitSelect2(selector) {
        var $el = $(selector);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({ width: '100%' });
    }

    function toggleProductionSelect() {
        var type = $('#production_type').val();

        // Hide all first and reset
        $('.production-select').removeClass('active');
        $('select[name="single_manufacturing_id"]').prop('required', false);
        $('select[name="batch_production_id"]').prop('required', false);
        $('#production-info-panel').hide();

        if (type === 'single_product') {
            $('#single-select').addClass('active');
            $('select[name="single_manufacturing_id"]').prop('required', true);
            // Re-init select2 after element is visible
            reinitSelect2('#single_manufacturing_id');
            $('#single_manufacturing_id').trigger('change');
        } else if (type === 'batch_conversion') {
            $('#batch-select').addClass('active');
            $('select[name="batch_production_id"]').prop('required', true);
            // Re-init select2 after element is visible
            reinitSelect2('#batch_production_id');
            $('#batch_production_id').trigger('change');
        }
    }

    function updateProductionInfo(selectElement) {
        var selected = $(selectElement).find(':selected');

        if (selected.val()) {
            $('#info-reference').text(selected.data('reference') || '-');
            $('#info-product').text(selected.data('product') || '-');
            $('#info-quantity').text(selected.data('quantity') || '-');
            $('#info-total-cost').text(selected.data('total-cost') || '-');
            $('#info-batch').text(selected.data('batch') || '-');
            $('#production-info-panel').show();
        } else {
            $('#production-info-panel').hide();
        }
    }

    // Run on page load
    toggleProductionSelect();

    // Run on production type change
    $('#production_type').on('change', toggleProductionSelect);

    // Update info panel when production is selected
    $('#single_manufacturing_id').on('change', function() {
        updateProductionInfo(this);
    });

    $('#batch_production_id').on('change', function() {
        updateProductionInfo(this);
    });
});
</script>
@endpush
