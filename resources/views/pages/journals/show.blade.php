@extends('layouts.backend.app')

@section('title', 'Journals')

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
                        <h4>New Journal </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            @can('journal.index')
                                <li class="breadcrumb-item"><a href="{{ route('journal.index') }}">Journals List</a></li>
                            @endcan
                            <li class="breadcrumb-item active">New Journal</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @can('journal.create')
                <a href="{{ route('journal.create') }}" class="btn btn-sm btn-secondary" style="margin-left: 2px;">
                    <span class="fa fa-plus-circle"> </span> New Journal
                </a>
            @endcan
            @can('journal.index')
                <a class="btn btn-secondary btn-sm" href="{{ route('journal.index') }}">
                    <span class="fa fa-list"></span> Journals List
                </a>
            @endcan
            <div class="container-fluid py-4">
                <div>
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                @if (session()->has('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                            </div>
                            <div class="col-12 mb-4">
                                <h5>Journal Details</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <p>Reference: {{ $journal->reference }}</p>
                                    <p>Date: {{ $journal->date }}</p>
                                    <p>Description: {{ $journal->description }}</p>
                                    <p>Created By: {{ $journal->createdBy->name ?? null }}</p>
                                    <p>Posted By: {{ $journal->postedBy->name ?? null }}</p>
                                    <p>Modified By: {{ $journal->updatedBy->name ?? null }}</p>
                                </div>
                                <div class="col-md-7 mb-3">

                                </div>

                                <div class="col-md-12">
                                    <div class="row">

                                        <div class="col-md-12">

                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Branch</th>
                                                        <th>Account</th>
                                                        <th>Debit</th>
                                                        <th>Credit</th>
                                                        <th>Description</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $total_credit = $total_debit = 0; ?>
                                                    @foreach ($journal->items as $journal_item)
                                                        <?php $total_credit += $journal_item->credit; ?>
                                                        <?php $total_debit += $journal_item->debit; ?>
                                                        <tr :wire:key="{{ $loop->index }}">
                                                            <td>{{ $journal_item->branch->name ?? ($journal->branch->name ?? "") }}</td>
                                                            <td>
                                                                {{ $journal_item->account()->code ?? $journal_item->account()->number }}
                                                                -
                                                                {{ $journal_item->account()->name ?? $journal_item->account()->description }}
                                                            </td>
                                                            <td>{{ currency_sign() . number_format($journal_item->debit, 2) }}
                                                            </td>
                                                            <td>{{ currency_sign() . number_format($journal_item->credit, 2) }}
                                                            </td>
                                                            <td>{{ $journal_item->description }}</td>
                                                            <td>{!! $journal->status == 0
                                                                ? '<span class="badge badge-danger">pending</span>'
                                                                : '<span class="badge badge-success">posted</span>' !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <h4>
                                                <small>Total Credit:</small>
                                                {{ currency_sign() . number_format($total_credit, 2) }} <br>
                                                <small>Total Debit:</small>
                                                {{ currency_sign() . number_format($total_debit, 2) }} <br>
                                                <small>Balance:</small>
                                                {{ currency_sign() . number_format($total_credit - $total_debit, 2) }}
                                            </h4>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <a href="{{ route('journal.print', $journal->id) }}" target="_blank"
                                                class="btn btn-dark btn-sm"><i class="fa fa-print"></i> Print</a>
                                            @if ($journal->status == 0)
                                                <a href="{{ route('journal.post', $journal->id) }}"
                                                    onclick="return confirm('Are you sure you want to post this journal?');"
                                                    class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Post</a>
                                                <a href="{{ route('journal.edit', $journal->id) }}"
                                                    class="btn btn-success btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                                <a href="{{ route('journal.delete', $journal->id) }}"
                                                    onclick="return confirm('Are you sure you want to delete this journal?');"
                                                    class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
                                            @else
                                                <a href="{{ route('journal.reverse', $journal->id) }}"
                                                    onclick="return confirm('Are you sure you want reverse this transaction?')"
                                                    class="btn btn-success btn-sm">Reverse</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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



@push('css')
    @livewireStyles
@endpush
@push('js')
    @livewireScripts
    <script></script>
@endpush
