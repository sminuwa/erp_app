@extends('layouts.backend.app')
@section('title', 'Payment Mode')

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/backend/plugins/datatables/datatables.css') }}">
@endpush
@section('content')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="btn-group">

                        <a class="btn btn-secondary btn-sm" href="{{ route('expenses.create') }}">
                            <span class="fa fa-plus"></span>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="{{ route('expenses.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="{{ route('expenses.index') }}">
                            <span class="fa fa-list"></span>
                        </a>
                        @if ($record->status != 'Cancelled')
                            <form onsubmit="return confirm('Are you sure you want to cancel?')"
                                action="{{ route('expenses.destroy', $record->id) }}" method="post"
                                style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary  btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i>
                                </button>
                            </form>
                        @else
                            <span class="fa fa-remove"></span>
                        @endif
                    </div>
                    @include('cards.expense')
                </div>
            </div>
        </section>
    </div>
@endSection
