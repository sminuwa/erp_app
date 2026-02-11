@extends('layouts.backend.app')

@section('title', 'View Production Team')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Production Team Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item">Manufacturing</li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.teams.index') }}">Teams</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @can('manufacturing.teams.create')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.create') }}">
                    <span class="fa fa-plus-circle"></span>
                </a>
            @endcan
            <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.index') }}">
                <span class="fa fa-list"></span>
            </a>
            @can('manufacturing.teams.edit')
                <a class="btn btn-secondary btn-sm" href="{{ route('manufacturing.teams.edit', $record->id) }}">
                    <span class="fa fa-pencil"></span>
                </a>
            @endcan
            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Team Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Team Name</th>
                                        <td>{{ $record->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Branch</th>
                                        <td>{{ $record->branch->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $record->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ledger Balance</th>
                                        <td>{{ number_format($record->ledger_balance, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Supervisors</h5>
                            </div>
                            <div class="card-body">
                                @if($record->supervisors->count() > 0)
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>User Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($record->supervisors as $index => $supervisor)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $supervisor->user ? ($supervisor->user->surname . ' ' . $supervisor->user->firstname) : 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted">No supervisors assigned.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Team Members</h5>
                            </div>
                            <div class="card-body">
                                @if($record->members->count() > 0)
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Staff Name</th>
                                                <th>Employment ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($record->members as $index => $member)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $member->staff->name ?? 'N/A' }}</td>
                                                    <td>{{ $member->staff->employment_id ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-muted">No team members assigned.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
@endpush
