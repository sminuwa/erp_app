@extends('layouts.backend.app')

@section('title', 'Products')

@push('css')
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Purchase of <small>products by {{ $model->supplier->name }}</small></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Purchase</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('purchases.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('purchases.index') }}">
                <span class="fa fa-list"></span>
            </a>
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-12'>
                        <div class='card'>
                            <div class="card-body">
                                @include('forms.purchase', [
                                    'route' => route('purchases.update', $model->id),
                                    'method' => 'PUT',
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@push('js')
    <!-- Sweet Alert Js -->
    <script src="{{ asset('assets/backend/js/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $(document).on("change", "#category_id", function(event) {
                $("#product_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadproducts') }}",
                    type: 'GET',
                    data: {
                        category_id: $("#category_id").val()
                    }
                }).done(function(msg) {
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
        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();
        $('.quantity,.price').keyup(function() {
            id = $(this).attr('data-value');
            //alert(id);
            delay(function() {
                $.ajax({
                    url: $('#' + id).attr('action'),
                    type: $('#' + id).attr('method'),
                    //dataType: 'json',
                    data: $('#' + id).serialize(),
                    success: function(data) {
                        id = id.substr(1);
                        subtotal = $('#price' + id).val() * $('#quantity' + id).val();
                        $('.subtotal' + id).text(formatMoney(subtotal));
                        $('#total').text(formatMoney(data));
                    },
                    error: function(xhr, err) {
                        //$('#total').text(formatMoney(data));
                        //$('#subtotal').text(formatMoney(data));
                    }
                });

            }, 500);
        });

        function formatMoney(n, c, d, t) {
            var c = isNaN(c = Math.abs(c)) ? 2 : c,
                d = d == undefined ? "." : d,
                t = t == undefined ? "," : t,
                s = n < 0 ? "-" : "",
                i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
                j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ?
                d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };
    </script>
@endpush
