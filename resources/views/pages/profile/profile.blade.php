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
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">

                        <h3 class="card-title">My Profile</h3>

                        <div class="row">
                            <div class="col-sm-2">
                                @if (Auth::check() && file_exists(public_path() . '/staffpics/' . Auth::user()->photo))
                                    <img alt="Photo" src="{{ url('staffpics/' . Auth::user()->photo) }}"
                                        class="rounded-full">
                                @else
                                    <img alt="Photo" src="{{ asset('staffpics/man.png') }}" style="width: 180px;height:213px;" class="rounded-full">
                                @endif
                            </div>
                            <div class="col-sm-2">
                                <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                                    {{ $user->name }}</div>
                                <div class="text-slate-500">
                                    {{ is_null($user->getUserRole) ? '' : $user->getUserRole->role->name }}
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="font-medium text-center lg:text-left lg:mt-3">Contact Details</div>
                                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                                    <div class="truncate sm:whitespace-normal flex items-center">
                                        <i data-lucide="mail" class="w-4 h-4 mr-2"></i>Email <small>
                                            {{ $user->email }}</small>
                                    </div>
                                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i> Phone <small>
                                            {{ $user->phone }}</small>
                                    </div>
                                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                                        <i data-lucide="home" class="w-4 h-4 mr-2"></i> Branch <small>
                                            {{ optional($user->branch)->name }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                            aria-controls="profile" aria-selected="true">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="picture-tab" data-toggle="tab" href="#picture" role="tab"
                            aria-controls="picture" aria-selected="false">Picture Upload</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="change-password-tab" data-toggle="tab" href="#change-password"
                            role="tab" aria-controls="change-password" aria-selected="false">Change Password</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="{{ route('profile.update', $user->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="{{ 'PUT' }}" />
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                                                    name="name" id="name" value="{{ old('name', $user->name) }}"
                                                    placeholder="" maxlength="255" required="required">
                                                @if ($errors->has('name'))
                                                    <div class="pristine-error text-danger mt-2">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    name="email" id="email" value="{{ old('email', $user->email) }}"
                                                    placeholder="" maxlength="255" required="required">
                                                @if ($errors->has('email'))
                                                    <div class="pristine-error text-danger mt-2">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                                    name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                                    placeholder="" maxlength="15" required="required">
                                                @if ($errors->has('phone'))
                                                    <div class="pristine-error text-danger mt-2">
                                                        <strong>{{ $errors->first('phone') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select type="text"
                                                    class="form-control {{ $errors->has('gender') ? ' is-invalid' : '' }}"
                                                    name="gender" required="required">
                                                    <option value="Male"
                                                        {{ old('gender', $user->gender) == 'Male' ? 'Selected' : '' }}>
                                                        Male
                                                    </option>
                                                    <option value="Female"
                                                        {{ old('gender', $user->gender) == 'Female' ? 'Selected' : '' }}>
                                                        Female
                                                    </option>
                                                </select>
                                                @if ($errors->has('gender'))
                                                    <div class="pristine-error text-danger mt-2">
                                                        <strong>{{ $errors->first('gender') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="branch_id">Branch</label>
                                                <select
                                                    class="form-control {{ $errors->has('branch_id') ? ' is-invalid' : '' }}"
                                                    name="branch_id" id="branch_id">
                                                    <option value="">Select</option>
                                                    @if (isset($branches))
                                                        @foreach ($branches as $data)
                                                            <option value="{{ $data->id }}"
                                                                {{ $data->id == $user->branch_id ? 'selected' : '' }}>
                                                                {{ $data->name }}</option>
                                                        @endforeach
                                                    @endif

                                                </select>
                                                @if ($errors->has('branch_id'))
                                                    <div class="pristine-error text-danger mt-2">
                                                        <strong>{{ $errors->first('branch_id') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="hidden" value="{{ $user->status }}" name="status" />
                                            <input type="hidden" value="{{ $user->user_code }}" name="user_code" />
                                            <div class="form-group text-right ">
                                                <input type="submit" class="btn btn-primary" value="Save" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="picture" role="picture" aria-labelledby="picture-tab">
                        <h2>Upload Picture</h2>
                        <form action="{{ route('upload.profile.picture') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="image" class="form-control"><br />
                            <input type="submit" value="Upload" class="btn btn-primary">
                        </form>
                    </div>
                    <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                        <h3>Change Password</h3>
                        <form name="search" id="search" method="POST"
                            action="{{ route('account.change.password', $user->id) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <div class="form-group">
                                        <label for="oldpwd">Old Password</label>
                                        <input type="password" class="form-control" id="oldpwd" name="oldpwd"
                                            placeholder="Old Password" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <div class="form-group">
                                        <label for="oldpwd">New Password</label>
                                        <input type="password" class="form-control" name="npwd" id="npwd"
                                            placeholder="New Password" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <div class="form-group">
                                        <label for="oldpwd">Re-type New Password</label>
                                        <input type="password" class="form-control" name="npwd_confirmation"
                                            id="npwd_confirmation" placeholder="Re-type New Password" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right ">
                                <input type="submit" class="btn btn-primary" value="Change" />

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@push('js')
@endpush
