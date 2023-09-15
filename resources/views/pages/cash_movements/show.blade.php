@extends('layouts.backend.app')
@section('title', 'Bank Withdraw & Deposit')

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
                        <h4>Bank Transaction Detail:
                            <small>{{ $record->type == 'Both' ? 'Withdraw & Deposit' : $record->type }}
                                Transaction</small>
                        </h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active">Bank Withdraw & Deposit</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <a class="btn btn-secondary btn-sm" href="{{ route('cash_movements.index') }}">
                <span class="fa fa-list"></span>
            </a>
            @if ($record->type == 'Deposit')
                <a class="btn btn-secondary btn-sm" href="{{ route('deposits.edit', $record->id) }}">
                    <span class="fa fa-pencil"></span>
                </a>
                <a class="btn btn-secondary btn-sm" href="{{ route('deposits.create') }}">
                    <span class="fa fa-plus"></span>
                </a>
                <form onsubmit="return confirm('Are you sure you want to delete?')"
                    action="{{ route('deposits.destroy', $record->id) }}" method="post" style="display: inline">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                        <i class="text-danger fa fa-remove"></i>
                    </button>
                </form>
            @elseif ($record->type == 'Both')
                <a class="btn btn-secondary btn-sm" href="{{ route('cash_movements.edit', $record->id) }}">
                    <span class="fa fa-pencil"></span>
                </a>

                <form onsubmit="return confirm('Are you sure you want to delete?')"
                    action="{{ route('cash_movements.destroy', $record->id) }}" method="post" style="display: inline">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                        <i class="text-danger fa fa-remove"></i>
                    </button>
                </form>
            @elseif ($record->type == 'Withdraw')
                <a class="btn btn-secondary btn-sm" href="{{ route('withdraw.edit', $record->id) }}">
                    <span class="fa fa-pencil"></span>
                </a>

                <form onsubmit="return confirm('Are you sure you want to delete?')"
                    action="{{ route('withdraw.destroy', $record->id) }}" method="post" style="display: inline">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                        <i class="text-danger fa fa-remove"></i>
                    </button>
                </form>
            @endif

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-8">
                        @include('cards.cash_movement')
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

@endsection
@push('js')
    <script type="text/javascript"></script>
@endpush
