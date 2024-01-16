@extends('layouts.backend.app')

@section('title', 'Journal')

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
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <input type="date" class="form-control datepicker" name="date" id="date" required>
                        <label class="floating-label">Date: @error('journal_date')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input name="description" id="description" type="text" class="form-control"
                            placeholder="Description">
                        <label class="floating-label">Description: @error('description')
                                <span class="text-danger error">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="button" data-toggle="modal" data-target="#journal_modal"
                            class="btn btn-primary float-right"><i class="fa fa-cart-plus"></i>
                            Add more
                        </button>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        @isset($total_credit)
                            <h4>
                                <small>Total Credit:</small> N{{ $total_credit }} <br>
                                <small>Total Debit:</small> N{{ $total_debit }} <br>
                                <small>Balance:</small> N{{ $total_credit - $total_debit }}
                            </h4>
                        @endisset
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <form action="{{ route('invoice.final_invoice') }}" method="post">
        @csrf
        <div class="modal fade" id="journal_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Journal
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group" wire:ignore>
                                                    <select name="type" id="type"
                                                        class="form-control  type select2-single" required>
                                                        <option value="">Select...</option>
                                                        <option value="Customer">Customer</option>
                                                        <option value="Supplier">Supplier
                                                        <option value="GeneralAccount">General Accounts
                                                        </option>
                                                    </select>
                                                    <label class="floating-label">Account Type: @error('type')
                                                            <span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <select name="account" id="account"
                                                        class="form-control select2-single ajax-general-accounts" required>
                                                        <option value="">Select...</option>
                                                    </select>
                                                    <label class="floating-label">Account: @error('account')
                                                            <span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input type="number" min="0" step=".01" class="form-control"
                                                        placeholder="Debit" debit="debit" id="debit"
                                                        wire:change="totals">
                                                    <label class="floating-label">Debit: @error('debit')
                                                            <br><span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-12">
                                                <div class="form-group" wire:ignore>
                                                    <input type="number" min="0" step=".01"
                                                        class="form-control" placeholder="Credit" name="credit"
                                                        id="credit" wire:change="totals">
                                                    <label class="floating-label">Credit: @error('credit')
                                                            <br><span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                </div>
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="Description"
                                                        name="description">
                                                    <label class="floating-label">Description: @error('description')
                                                            <br><span class="text-danger error">{{ $message }}</span>
                                                        @enderror
                                                    </label>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                {{-- <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                </div> --}}

                            </div>
                        </div>
                    </div>
                    {{-- <input type="hidden" name="customer_id" value="{{ $customer->id }}"> --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection



@push('css')
    @livewireStyles
@endpush
@push('js')
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded')
            /*$(".select2-single, .select2-multiple, .select2").select2({
                theme: "bootstrap",
                maximumSelectionSize: 6,
                containerCssClass: ':all:'
            });*/
        })
    </script>
@endpush
