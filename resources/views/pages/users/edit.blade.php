@extends('layouts.backend.app')

@section('title', 'Manage User')

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
						<h4>Edit User Account of <small>{{$model->name}}</small></h4>
					</div>
					<div class="col-sm-6">
						<ol class="breadcrumb float-sm-right">
							<li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
							<li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>
							<li class="breadcrumb-item active">Edit</li>
						</ol>
					</div>
				</div>
			</div><!-- /.container-fluid -->
		</section>

		<!-- Main content -->
		<section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('users.create') }}">
                <span class="fa fa-plus-circle"> New User</span>
            </a>
			<a class="btn btn-secondary btn-sm" href="{{ route('users.import.form') }}">
                <span class="fa fa-upload"> Upload Users</span>
            </a>
            <a class="btn btn-secondary btn-sm" href="{{ route('users.index') }}">
                <span class="fa fa-list"> View Users</span>
            </a>
			<div class="container-fluid">
                <div class="row">
                    <div class='col-md-4'>
                        <div class='card'>
                            
                            <div class="card-body">
                                @include('forms.user', [
                                    'route' => route('users.update', $model->id),
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

