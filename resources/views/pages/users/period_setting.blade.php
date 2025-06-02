@extends('layouts.backend.app')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <h4>Manage Entry Access Period</h4>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.period.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="period_open" class="form-label">Period Open</label>
                        <input type="text" name="period_open" id="period_open" class="form-control datepicker"
                            value="{{ old('period_open', optional($period)->period_open) }}">
                    </div>

                    <div class="mb-3">
                        <label for="period_close" class="form-label">Period Close</label>
                        <input type="text" name="period_close" id="period_close" class="form-control datepicker"
                            value="{{ old('period_close', optional($period)->period_close) }}">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="reset_user_range"
                            name="reset_range">
                        <label class="form-check-label" for="reset_user_range">
                            Reset Date Range to No Limit
                        </label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Save Period</button>

                </form>
            </div>
        </section>
    </div>
@endsection
