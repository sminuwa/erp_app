@extends('layouts.backend.app')

@section('title', 'Manage Area Managers')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Manage Area Managers</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Area Managers</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <a href="{{ route('area_managers.create') }}" class="btn btn-sm btn-secondary mb-3"><span
                        class="fa fa-plus-circle"></span> New Manager</a>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Area Managers List</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Manager Code</th>
                                    <th>Manager Name</th>
                                    <th>Assigned Branches</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach ($areaManagers->groupBy('manager_id') as $managerId => $managerGroup)
                                    @php $manager = $managerGroup->first(); @endphp
                                    <tr>
                                        <td>{{ $manager->manager->user_code ?? 'N/A' }}</td>
                                        <td>{{ $manager->manager->name }}</td>
                                        <td>
                                            @foreach ($managerGroup as $branch)
                                                <span class="badge badge-info">{{ $branch->branch->code }}-{{ $branch->branch->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $manager->status ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <a href="{{ route('area_managers.edit', $manager->id) }}"
                                                class="btn btn-sm btn-primary"><span class="fa fa-edit"></span></a>
                                            <form action="{{ route('area_managers.destroy', $manager->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><span class="fa fa-trash"></span></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
