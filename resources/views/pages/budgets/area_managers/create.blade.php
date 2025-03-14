@extends('layouts.backend.app')

@section('title', isset($areaManager) ? 'Edit Area Manager' : 'Create Area Manager')

@push('css')
@endpush

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>{{ isset($areaManager) ? 'Edit Area Manager' : 'Create Area Manager' }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">
                                {{ isset($areaManager) ? 'Edit Area Manager' : 'Create Area Manager' }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <a href="{{ route('area_managers.index') }}" class="btn btn-sm btn-secondary mb-3"><span
                        class="fa fa-list"></span> Managers</a>
                    <div class="card-header">{{ isset($areaManager) ? 'Edit Area Manager' : 'Assign New Area Manager' }}
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($areaManager) ? route('area_managers.update', $areaManager->id) : route('area_managers.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($areaManager))
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="manager_id">Manager</label>
                                <select name="manager_id" class="form-control select2-single" required>
                                    <option>Select Manager</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}"
                                            {{ isset($areaManager) && $areaManager->manager_id == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->user_code }}-{{ $manager->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="branch_id">Assign Branches</label>
                                <select name="branch_id[]" class="form-control select2-single" multiple required>
                                    <option value="">Select Branches</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ isset($areaManager) && in_array($branch->id, $areaManager->branches->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $branch->code }}-{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="1"
                                        {{ isset($areaManager) && $areaManager->status == 1 ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="0"
                                        {{ isset($areaManager) && $areaManager->status == 0 ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>

                            <button type="submit"
                                class="btn btn-primary">{{ isset($areaManager) ? 'Update' : 'Assign' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
