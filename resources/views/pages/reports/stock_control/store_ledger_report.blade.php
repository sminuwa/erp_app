@extends('layouts.backend.app')

@section('title', 'Store Ledger Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption { caption-side: top; }
        #load .store-ledger-empty { padding: 3rem; text-align: center; color: #6c757d; background: #f8f9fa; border-radius: 0.25rem; }
        .store-ledger-filters .form-group label { font-weight: 600; color: #495057; margin-bottom: 0.35rem; }
        .store-ledger-filters .form-control { border-radius: 0.25rem; }
        .store-ledger-filters .card-body { padding: 1.25rem 1.5rem; }
        .min-height-200 { min-height: 200px; }
        .store-ledger-results { padding: 1rem 1.25rem; }
        .store-ledger-results .table { font-size: 0.9rem; }
        .store-ledger-results .table th { white-space: nowrap; }
    </style>
@endpush

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Store Ledger Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('store.ledger.reports') }}">Reports</a></li>
                            <li class="breadcrumb-item active">Store Ledger</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                {{-- Filters --}}
                <div class="card card-outline card-primary shadow-sm store-ledger-filters">
                    <div class="card-header py-2">
                        <h5 class="card-title mb-0"><i class="fas fa-filter mr-1"></i> Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="store-ledger-form">
                            <div class="row align-items-end">
                                <div class="col-md-2 col-lg-2 form-group mb-2 mb-md-0">
                                    <label for="company_id">Company</label>
                                    <select class="form-control form-control-sm select2-single ajax-companies {{ $errors->has('company_id') ? ' is-invalid' : '' }}"
                                            name="company_id" id="company_id" required></select>
                                </div>
                                <div class="col-md-2 col-lg-2 form-group mb-2 mb-md-0">
                                    <label for="branch_id">Branch</label>
                                    <select class="form-control form-control-sm select2-single ajax-branches {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                            name="branch_id" id="branch_id" required></select>
                                </div>
                                <div class="col-md-2 col-lg-2 form-group mb-2 mb-md-0">
                                    <label for="store_id">Store</label>
                                    <select class="form-control form-control-sm select2-single ajax-stores {{ $errors->has('store_id') ? ' is-invalid' : '' }}"
                                            name="store_id" id="store_id"></select>
                                </div>
                                <div class="col-md-2 col-lg-2 form-group mb-2 mb-md-0">
                                    <label for="category_id">Category</label>
                                    <select class="form-control form-control-sm select2-multiple ajax-categories {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                                            name="category_id[]" id="category_id" multiple="multiple"></select>
                                </div>
                                <div class="col-md-2 col-lg-2 form-group mb-2 mb-md-0">
                                    <label for="product_id">Product</label>
                                    <select class="form-control form-control-sm select2-single ajax-products {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                                            name="product_id" id="product_id"></select>
                                </div>
                                <div class="col-md-2 col-lg-2 form-group mb-2 mb-md-0">
                                    <button type="button" class="btn btn-primary btn-block" id="generate" name="generate">
                                        <i class="fas fa-sync-alt mr-1"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Results area --}}
                <div class="card card-outline card-secondary shadow-sm mt-3">
                    <div class="card-body p-0 position-relative min-height-200">
                        <div id="img-loader" class="position-absolute d-none" style="top:50%;left:50%;transform:translate(-50%,-50%);z-index:5;">
                            <img src="{{ asset('assets/backend/img/loader.png') }}" alt="Loading" width="64" height="64">
                        </div>
                        <div id="load"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('js')

    <script type="text/javascript">
        $(function () {
            function formatMoney(n, c, d, t) {
                var c = isNaN(c = Math.abs(c)) ? 0 : c,
                    d = d == undefined ? "." : d,
                    t = t == undefined ? "," : t,
                    s = n < 0 ? "-" : "",
                    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                    j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                    d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };

            $('#generate').on("click", function () {
                var product_id = $('#product_id').val();
                var store_id = $('#store_id').val();
                var category_id = $('#category_id').val();
                var company_id = $('#company_id').val();
                var branch_id = $('#branch_id').val();

                $('#img-loader').removeClass('d-none').addClass('d-block');
                $.ajax({
                    type: "GET",
                    url: "{{ route('ajax.load.store.ledger.reports') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: product_id,
                        store_id: store_id,
                        category_id: category_id,
                        company_id: company_id,
                        branch_id: branch_id
                    }
                }).done(function (data) {
                    $('#img-loader').removeClass('d-block').addClass('d-none');
                    $("#load").html(data);
                    if (typeof loadDataTable2 === 'function') loadDataTable2();
                }).fail(function () {
                    $('#img-loader').removeClass('d-block').addClass('d-none');
                    $("#load").html('<div class="store-ledger-empty"><i class="fas fa-exclamation-triangle fa-2x mb-2"></i><p class="mb-0">Failed to load report. Please try again.</p></div>');
                });
            });
        });
    </script>
@endpush
