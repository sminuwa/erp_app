@extends('layouts.backend.app')

@section('title', 'Stock Adjustment')

@push('css')
@endpush

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Stock Adjustment</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Stock Adjustment</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                @can('stock_adjustments.create')
                    <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.create') }}">
                        <span class="fa fa-plus-circle"></span> New
                    </a>
                @endcan
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-striped" id="record1">
                                    <thead>
                                        <tr>
                                            <th>Date </th>
                                            <th>Reference </th>
                                            <th>Operation </th>
                                            <th>Description </th>
                                            <th>Created By </th>
                                            <th>Date Created </th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $record)
                                            <tr class="@if ($record->status == 0) bg-warning @endif">
                                                <td> {{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }}
                                                </td>
                                                <td> {{ $record->reference }} </td>
                                                <td class="text-center">
                                                    {!! $record->operation == 'in'
                                                        ? '<span class="badge badge-success">In</span>'
                                                        : '<span class="badge badge-danger">Out</span>' !!}
                                                </td>
                                                <td> {{ $record->description ?? null }} </td>
                                                <td> {{ $record->createdBy->name ?? null }} </td>
                                                <td> {{ \Carbon\Carbon::parse($record->created_at)->toFormattedDateString() }}
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                            id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @can('stock_adjustments.show')
                                                                <a class="dropdown-item"
                                                                    href="{{ route('stock_adjustments.show', $record->id) }}">
                                                                    <span class="fa fa-eye"></span> View
                                                                </a>
                                                            @endcan
                                                            @can('stock_adjustments.print')
                                                                <a class="dropdown-item"
                                                                    href="{{ route('stock_adjustments.print', $record->id) }}"
                                                                    title="Print GRN" target="_BLANK">
                                                                    <span class="fa fa-print"></span> Print
                                                                </a>
                                                            @endcan
                                                            @if ($record->status == 0)
                                                                @can('stock_adjustments.post')
                                                                    <form
                                                                        action="{{ route('stock_adjustments.post', $record->id) }}"
                                                                        method="post"
                                                                        onsubmit="return confirm('Are you sure you want to post this order?')">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fa fa-check" aria-hidden="true"></i> Post
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                                @can('stock_adjustments.edit')
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('stock_adjustments.edit', $record->id) }}">
                                                                        <span class="fa fa-pencil"></span> Edit
                                                                    </a>
                                                                @endcan
                                                                @can('stock_adjustments.delete')
                                                                    <form
                                                                        onsubmit="return confirm('Are you sure you want to cancel?')"
                                                                        action="{{ route('stock_adjustments.delete', $record->id) }}"
                                                                        method="post" style="display: inline">
                                                                        {{ csrf_field() }}
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="text-danger fa fa-remove"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
        $(function() {
            $("#record1").DataTable({
                'iDisplayLength': 100
            });
            $('#record2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
@endpush
