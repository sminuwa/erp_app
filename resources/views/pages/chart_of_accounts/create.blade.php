@extends('layouts.backend.app')
@section('title', 'Manage Chart of Accounts')

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
						<h4>Add Chart of Accounts</h4>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
							<li class="breadcrumb-item"><a href="{{route('chart_of_accounts.index')}}">Chart of Accounts</a></li>
							<li class="breadcrumb-item active">Create</li>
						</ol>
					</div>
				</div>
			</div><!-- /.container-fluid -->
		</section>

		<!-- Main content -->
		<section class="content">
			<a class="btn btn-secondary btn-sm" href="{{ route('chart_of_accounts.create') }}">
                <span class="fa fa-plus-circle">New CoA</span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('chart_of_accounts.index') }}">
                <span class="fa fa-list"> View CoAs</span>
            </a><br/>
			<div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        @include('forms.chart_of_account')
                    </div>
                </div>
			</div><!-- /.container-fluid -->
		</section>
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->

@endsection
@push('js')    
	<script type="text/javascript">

	</script>
@endpush

