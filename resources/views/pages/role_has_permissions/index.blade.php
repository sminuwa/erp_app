@extends('layouts.backend.app')

@section('title', 'Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption {
            caption-side: top;
        }
    </style>
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                @can('roles.create')
                    <a class="btn btn-secondary tooltip" title="Add new role" href="{{ route('roles.create') }}">
                        <span data-lucide="plus-circle" class="text-succcess">Add New</span>
                    </a>
                @endcan
                <div class="intro-y flex items-center mt-8">
                    <h2 class="text-lg font-medium mr-auto">Assign Permission to Role</h2>
                </div>
                <div class="grid grid-cols-12 gap-6 mt-5">
                    <div class="intro-y col-span-12 lg:col-span-6">
                        <div class="intro-y box">
                            <div id="input" class="p-5">
                                <div class="preview">
                                    <div class="row">
                                        <div class='col-md-12'>
                                            <form action="{{ route('role-permission.store') }}" id="roleperm"
                                                name="roleperm" method="POST">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="faculty">Role:</label>
                                                        <select
                                                            class="form-control {{ $errors->has('role') ? 'is-invalid' : 'is-valid' }}"
                                                            name="role" id="role"
                                                            value="{{ isset($model->name) ? $model->name : '' }}"
                                                            placeholder="Role Name" required>
                                                            <option value="">Select...</option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}"
                                                                    {{ (isset($model) ? $model->role : 0) == $role->id ? 'selected' : '' }}>
                                                                    {{ $role->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('role'))
                                                            <div class="invalid-feedback" style="color:red;">
                                                                {{ $errors->first('role') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-2 mb-2 text-right">
                                                        <div class="form-group">
                                                            <label for="">&nbsp;</label>
                                                            <button type="button" name="add" id="addbtn"
                                                                class="btn btn-primary text-right"><span class="ti-save">
                                                                    Save
                                                                </span></button>
                                                            @if (isset($model) && $model != null)
                                                                <button type="button" id="close" name="close"
                                                                    class="btn btn-danger btn-sm"><span
                                                                        class="ti-close"></span>
                                                                    Close
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div><!-- end of form row-->
                                                <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                                                    <div class="col-span-12 lg:col-span-12">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="preview">
                                                                    <div class="overflow-x-auto">
                                                                        <div id="display" class="table-responsive">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#close').click(function() {
                window.location.href = "{{ url('/role/permission') }}";
            });
            $('#role').on("change", function() {
                var role = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('role-permission.show') }}",
                    data: {
                        role_id: role
                    }
                }).done(function(data) {

                    $('#display').html(data);
                    $("#checkAll").click(function() {
                        $('input:checkbox').not(this).prop('checked', this.checked);
                    });
                });
            });
            $('#addbtn').on("click", function() {
                $.ajax({
                    type: "POST",
                    url: "{{ route('role-permission.store') }}",
                    data: $('#roleperm').serialize()
                }).done(function(data) {
                    alert(data);
                });
            });

        });
    </script>
@endpush
