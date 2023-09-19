<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Albabello" name="Albabello" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/backend/img/favicon.ico') }}" type="image/x-icon">
    <title>@yield('title') - {{ config('app.name', 'Albabello') }}</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- IonIcons -->
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/backend/css/toastr.min.css') }}">

    <link rel="icon" href="{{ asset('assets/backend/img/policymaker.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/backend/css/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/backend/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/select2-bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datepicker/datepicker3.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datepicker/jquery.datetimepicker.css') }}">



    @stack('css')

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @auth
            @include('layouts.backend.partial.navbar')
        @endauth
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @auth
            @include('layouts.backend.partial.sidebar')
        @endauth
        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        @include('layouts.backend.partial.footer')

    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="{{ asset('assets/backend/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE -->
    <script src="{{ asset('assets/backend/js/adminlte.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script src="{{ asset('assets/backend/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/backend/js/select2.full.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    {{--<script src="{{ asset('assets/backend/plugins/datepicker/datepicker-bootstrap.min.js') }}"></script> --}}
    <script src="{{ asset('assets/backend/plugins/datepicker/moment-with-locales.js') }}"></script>
    <script src="{{ asset('assets/backend/plugins/datepicker/jquery.datetimepicker.js') }}"></script>
    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}', 'Error!!', {
                    closeButton: true,
                    progressBar: true,
                });
            @endforeach
        @endif

        @if (Session::has('app_message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('app_message') }}");
        @endif

        @if (Session::has('app_error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('app_error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ session('warning') }}");
        @endif

        $(".select2-single, .select2-multiple").select2({
            theme: "bootstrap",
            maximumSelectionSize: 6,
            containerCssClass: ':all:'
        });

        $(":checkbox").on("click", function() {
            $(this).parent().nextAll("select").prop("disabled", !this.checked);
        });

        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        $('.datetimepicker').datetimepicker({
            datepicker: false,
            format: 'H:i',
            step: 5
        });
    </script>

    @stack('js')

</body>



</html>
