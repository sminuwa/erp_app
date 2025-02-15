@extends('layouts.backend.app')
@section('title', 'Purchase Panel')

@push('css')
@endpush

@section('content')
    <input name="cart_page_type" type="hidden" value="grn">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Purchase (GRN)</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Purchases</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container">
                @can('purchases.create')
                    <a class="btn btn-secondary btn-sm" href="{{ route('purchases.create') }}">
                        <span class="fa fa-plus-circle"> New Purchase (GRN)</span>
                    </a>
                @endcan
                @can('purchases.index')
                    <a class="btn btn-secondary btn-sm" href="{{ route('purchases.index') }}">
                        <span class="fa fa-list"> Purchases</span>
                    </a>
                @endcan
                <div class="row">
                    <div class='col-md-12'>
                        @include('forms.purchase')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@push('js')
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $("#category_link,#product_link,#supplier_link").on('click', function () {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'shortcut',
                    name: 'shortcut'
                }).appendTo('form');
            });
            $(document).on("change", "#category_id", function (event) {
                $("#product_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadproducts') }}",
                    type: 'GET',
                    data: {
                        category_id: $("#category_id").val()
                    }
                }).done(function (msg) {
                    $("#product_id").html("<option value=''>--select--</option>" + msg);
                });
            });
        });

        function deleteItem(id) {
            const swalWithBootstrapButtons = swal.mixin({
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
            })

            swalWithBootstrapButtons({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons(
                        'Cancelled',
                        'Your data is safe :)',
                        'error'
                    )
                }
            })
        }

        function formatNumber(input) {
            // Remove non-numeric and non-decimal characters
            let value = input.value.replace(/[^\d.]/g, '');

            // Split the value into integer and decimal parts
            const parts = value.split('.');
            let integerPart = parts[0] ? parseFloat(parts[0]) : 0;
            let decimalPart = parts[1] !== undefined ? '.' + parts[1] : '';

            // Check if the integer part is not NaN
            if (!isNaN(integerPart)) {
                // Format the integer part with commas and dot as decimal separator
                integerPart = integerPart.toLocaleString('en-US', {
                    maximumFractionDigits: 2,
                    useGrouping: true
                });

                // Set the formatted value back to the input
                input.value = integerPart + decimalPart;
            }
        }
    </script>
@endpush
