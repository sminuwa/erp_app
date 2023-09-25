@extends('layouts.backend.app')

@section('title', 'Modify General Account')

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
						<h1>General Accounts</h1>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
							<li class="breadcrumb-item"><a href="{{route('general_accounts.index')}}">General Accunts</a></li>
							<li class="breadcrumb-item active">Modify</li>
						</ol>
					</div>
				</div>
			</div><!-- /.container-fluid -->
		</section>

		<!-- Main content -->
		<section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.create') }}">
                <span class="fa fa-plus-circle"></span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('general_accounts.index') }}">
                <span class="fa fa-list"></span>
            </a><br/>
			<div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>
                            
                            <div class="card-body">
                                @include('forms.general_account', [
                                    'route' => route('general_accounts.update', $model->id),
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
    
	<script type="text/javascript">


	</script>

@endpush

