@extends('layouts.backend.app')
@section('title', 'Customer')

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
                        <h4>Add Customer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Customers</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('customers.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.create') }}">
                    <span class="fa fa-plus-circle"> New Customer</span>
                </a>
            @endcan
            @can('customers.import.form')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.import.form') }}">
                    <span class="fa fa-upload"> Upload Customers</span>
                </a>
            @endcan
            @can('customers.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('customers.index') }}">
                    <span class="fa fa-list"> View Customers</span>
                </a>
            @endcan
            <div class="container-fluid">
                <div class="row">
                    <div class='col-md-4 col-offset-4'>
                        
                        <form action="{{ isset($route) ? $route : route('customers.store') }}" method="POST">
                            {{ csrf_field() }}
                        {{--    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />--}}
                            <div class="form-group">
                                <label for="code">Branch</label>
                                <select class="form-control ajax-branches  {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                        name="branch_id" id="branch_id"
                                        selected_item="{{ $model->branch_id }}"
                                        required></select>
                                @if ($errors->has('branch_id'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('branch_id') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name"
                                    value="{{ old('name', $model->name) }}" placeholder="" maxlength="50" required="required">
                                @if ($errors->has('name'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="account_type">Account Type</label>
                                <select class="form-control {{ $errors->has('account_type') ? ' is-invalid' : '' }}" name="account_type" id="account_type" required>
                                    <option value="">Select...</option>
                                    <option value="R" {{old('account_type', $model->account_type)}} {{ $model->type =="Retail" ? 'selected':'' }}>Retail</option>
                                    <option value="W" {{old('account_type', $model->account_type)}} {{ $model->type =="Wholesale" ? 'selected':'' }}>Wholesale</option>
                                </select>
                                @if ($errors->has('account_type'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('account_type') }}</strong>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Customer Category</label>
                                <input type="radio" class="category" name="category" value="staff" required="required"
                                    {{ old('category', substr($model->category, 0, 2)) == 'staff' ? 'checked' : '' }} /> Staff
                                <input type="radio" class="category" name="category"  checked
                                    {{ old('category', substr($model->category, 0, 2)) == 'non-staff' ? 'checked' : '' }} value="non-staff"
                                    required="required" /> Non-staff
                                @if ($errors->has('category'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="staff_number_field"></div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                    id="email" value="{{ old('email', $model->email) }}" placeholder="" maxlength="191">
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone"
                                    id="phone" value="{{ old('phone', $model->phone) }}" placeholder="" maxlength="191" required="required">
                                @if ($errors->has('phone'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" name="address"
                                    id="address" value="{{ old('address', $model->address) }}" placeholder="" maxlength="191"
                                    required="required">
                                @if ($errors->has('address'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="credit_limit">Credit Limit</label>
                                <input type="text" class="form-control {{ $errors->has('credit_limit') ? ' is-invalid' : '' }}"
                                    name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $model->credit_limit) }}"
                                    placeholder="" value="0">
                                @if ($errors->has('credit_limit'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('credit_limit') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="code">Status</label>
                                <select class="form-control {{ $errors->has('status') ? ' is-invalid' : '' }}"
                                        name="status" id="status"
                                        required>
                                    <option value="1" @if($model->status == 1) selected @endif>Active</option>
                                    <option value="0" @if($model->status == 0) selected @endif>Inactive</option>
                                </select>
                                @if ($errors->has('status'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <?php $officers = App\Models\User::orderBy('user_code', 'asc')->get(); ?>
                                <label for="code">Relation Officer</label>
                                <select class="form-control ajax-users {{ $errors->has('relation_officer') ? ' is-invalid' : '' }}"
                                        name="relation_officer" id="relation_officer"
                                        required>
                                    @foreach($officers as $officer)
                                        <option value="{{$officer->id}}" {{ $model->relation_officer == $officer->id ? 'selected' :'' }}>{{ $officer->user_code }} - {{ $officer->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('relation_officer'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('relation_officer') }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group text-right ">

                                <input type="submit" class="btn btn-primary" value="Save" />

                            </div>
                        </form>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection
@push('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
{{--    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>--}}
<script>
    $(document).ready(function () {
        $('input[name="category"]').on('change', function () {
            // Get the selected radio button's value
            const category = $('input[name="category"]:checked').val();

            if(category == 'staff'){
                $('.staff_number_field').html(`

                    <div class="form-group" >
                        <label for="staff_no">Staff No.</label>
                        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
                            id="code" value="{{ old('phone', $model->code) }}" placeholder="Staff No" required="required">
                        @if ($errors->has('code'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('code') }}</strong>
                            </div>
                        @endif
                    </div>
                
                `)
            }else{
                 $('.staff_number_field').html('');
            }
            // Log the value or perform any action
            console.log("Selected Value: ", selectedValue);
        });
    });
</script>
    <script type="text/javascript">
        $(function() {
            $(document).on("change", "#bank_id", function(event) {
                $("#bank_branch_id").html(" < option value = '' > Loading... < /option>");
                $.ajax({
                    url: "{{ route('ajax.loadbranches') }}",
                    type: 'GET',
                    data: {
                        bank_id: $("#bank_id").val()
                    }
                }).done(function(msg) {
                    $("#bank_branch_id").html("<option value=''>--select--</option>" + msg);
                });
            });

            $(document).on("change", "#account_type", function(event) {
                $("#code").val("Loading...");
                $.ajax({
                    url: "{{ route('generate.customerCode') }}",
                    type: 'GET',
                    data: {
                        account_type: $("#account_type").val()
                    }
                }).done(function(msg) {
                    // $("#code").val(msg);
                });
            });
        });
    </script>
@endpush
