@extends('layouts.backend.app')

@section('title', 'Report')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
    <style>
        caption {
            caption-side: top;
        }

        /* Loader CSS */
        .loader {
            display: none;
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }

        .loader img {
            width: 50px;
            height: 50px;
        }
    </style>
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
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
                                                method="POST">
                                                @csrf
                                                <div class="form-row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="faculty">Role:</label>
                                                        <select class="form-control" name="role" id="role" required>
                                                            <option value="">Select...</option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}">{{ $role->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 mb-2 text-right">
                                                        <div class="form-group">
                                                            <label for="">&nbsp;</label>
                                                            <button type="button" name="add" id="addbtn"
                                                                class="btn btn-primary">
                                                                <span class="ti-save"> Save</span>
                                                            </button>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="intro-y grid grid-cols-12 gap-6 mt-5">
                                                    <div class="col-span-12 lg:col-span-12">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="preview">
                                                                    <div class="overflow-x-auto">
                                                                        <div id="display" class="table-responsive">
                                                                        </div>
                                                                        <!-- Loader -->
                                                                        <div class="loader" id="loader">
                                                                            <img src="{{ asset('assets/backend/img/loader.png') }}"
                                                                                alt="Loading...">
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
    <script>
        $(document).ready(function() {

            $('#role').on("change", function() {
                var role = $(this).val();
                if (role) {
                    $("#loader").show(); // Show loader
                    $.ajax({
                        type: "GET",
                        url: "{{ route('role-permission.show') }}",
                        data: {
                            role_id: role
                        }
                    }).done(function(data) {
                        $("#loader").hide(); // Hide loader
                        $('#display').html(data);
                        $("#checkAll").click(function() {
                            $('input:checkbox').not(this).prop('checked', this.checked);
                        });
                        // Live Search for Permissions Table
                        $("#searchPermissions").on("keyup", function() {
                            var value = $(this).val()
                                .toLowerCase(); // Convert input to lowercase
                            $("#record1 tbody tr").filter(function() {
                                $(this).toggle($(this).text().toLowerCase().indexOf(
                                    value) > -1) // Hide/Show rows
                            });
                        });

                    }).fail(function() {
                        $("#loader").hide(); // Hide loader on error
                        alert("Error loading permissions.");
                    });
                }
            });

            $('#addbtn').on("click", function() {
                $("#loader").show(); // Show loader
                $.ajax({
                    type: "POST",
                    url: "{{ route('role-permission.store') }}",
                    data: $('#roleperm').serialize()
                }).done(function(data) {
                    $("#loader").hide(); // Hide loader
                    alert(data);
                }).fail(function() {
                    $("#loader").hide(); // Hide loader on error
                    alert("Error saving permissions.");
                });
            });

        });
    </script>
@endpush
