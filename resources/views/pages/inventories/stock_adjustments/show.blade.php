@extends('layouts.backend.app')
@section('title', 'Payment Mode')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush
@section('content')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Stock Adjustment Details</h4>
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

        <section class="content">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-right">
                        <a class="btn btn-warning btn-sm" href="javascript:history.back();">
                            <span class="fa fa-arrow-left"></span> Back
                        </a>
                        @can('stock_adjustments.create')
                            <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.create') }}">
                                <span class="fa fa-plus"></span> New
                            </a>
                        @endcan
                        @can('stock_adjustments.index')
                            <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.index') }}">
                                <span class="fa fa-list"></span> List
                            </a>
                        @endcan
                        @can('stock_adjustments.print')
                            <a class="btn btn-secondary btn-sm" target="_blank"
                                href="{{ route('stock_adjustments.print', $record->id) }}">
                                <span class="fa fa-print"></span> Print
                            </a>
                        @endcan
                        @if ($record->status == 0)
                            @can('stock_adjustments.edit')
                                <a class="btn btn-secondary btn-sm" href="{{ route('stock_adjustments.edit', $record->id) }}">
                                    <span class="fa fa-pencil"></span> Edit
                                </a>
                            @endcan
                            @can('stock_adjustments.post')
                                <form onsubmit="return confirm('Are you sure you want to post this stock record?')"
                                    action="{{ route('stock_adjustments.post', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-success  btn-sm cursor-pointer">
                                        <i class="fa fa-check"></i> Post
                                    </button>
                                </form>
                            @endcan
                            @can('stock_adjustments.delete')
                                <form onsubmit="return confirm('Are you sure you want to delete this record?')"
                                    action="{{ route('stock_adjustments.delete', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger  btn-sm cursor-pointer">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Stock Adjustment Details :: {{ $record->reference }}
                                    ({{ $record->status === 0 ? 'Pending' : 'Posted' }})</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Branch</th>
                                            <td colspan="5">
                                                {{ ($record->branch->code ?? null) . ' - ' . ($record->branch->name ?? null) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ optional($record->date)->toDayDateTimeString() }}</td>
                                            <th>Operation</th>
                                            <td>{!! $record->operation == 'in'
                                                ? '<span class="badge badge-success">In</span>'
                                                : '<span class="badge badge-danger">Out</span>' !!}</td>
                                            <th>Status</th>
                                            <td>{!! $record->status == 1
                                                ? '<span class="badge badge-success">Posted</span>'
                                                : '<span class="badge badge-warning">Pending</span>' !!}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td colspan="5">{{ $record->description ?? null }} </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    Products
                                </h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Store</th>
                                            <th>Product</th>
                                            <th>Expiry Date</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $total = 0; @endphp
                                        @foreach ($record->products as $product)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>{{ $product->store?->code . ' - ' . $product->store?->name }}</td>
                                                <td>{{ $product->product?->code . ' - ' . $product->product?->name }}
                                                    {{ $product->product->category->code ?? 'NULL NULL' }}
                                                </td>
                                                <td>{{ $product->expiry_date ?? 'N/A' }}</td>
                                                <td>{{ $product->quantity }}</td>
                                                <td class="text-right">
                                                    {{ currency_sign() . number_format($product->cost_price, 2) }}</td>
                                                @php $total += $product->cost_price * $product->quantity; @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" style="text-align: right">Total</th>
                                            <th style="text-align: right">{{ currency_sign() . number_format($total, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endSection
