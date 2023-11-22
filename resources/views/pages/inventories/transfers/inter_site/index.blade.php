@extends('layouts.backend.app')

@section('title', 'Stock Tranfer')

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
                        <h4>Intersite Stock Transfer</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stores.index') }}">Store</a></li>
                            <li class="breadcrumb-item active">Stock Tranfer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <div class="container">
                <a class="btn btn-secondary btn-sm" href="{{ route('intersite.create') }}">
                    <span class="fa fa-plus-circle"></span> New Intersite
                </a>
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form action="{{route('intersite.search')}}" method="POST">
                                    @csrf
                                    <div class="input-group">
                                        <input type="search" class="form-control rounded" required placeholder="Search" name="refno" aria-label="Search" aria-describedby="search-addon" />
                                        <button type="submit" class="btn btn-outline-primary">search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @isset($records)
                                    <table class="table table-bordered table-striped" id="record1">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Created By</th>
                                            <th>Posted By</th>
                                            <th>Received By</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($records as $intersite)
                                            <tr>
                                                <td> {{ $intersite->date }} </td>
                                                <td> {{ $intersite->reference }} </td>
                                                <td> {{ $intersite->source->code .' - '.$intersite->source->name }} </td>
                                                <td> {{ $intersite->destination->code .' - '.$intersite->destination->name }} </td>
                                                <td> {{ $intersite->createdBy->name ?? '' }} </td>
                                                <td> {{ $intersite->postedBy->name ?? '' }} </td>
                                                <td> {{ $intersite->receivedBy->name ?? ''}}</td>
                                                <td> {{ $intersite->status }} </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item" href="{{ route('intersite.show', $intersite->id) }}">
                                                                <span class="fa fa-eye"></span> View
                                                            </a>
                                                            <a class="dropdown-item" title="Print" target="_BLANK"
                                                               href="{{ route('intersite.print', $intersite->id) }}">
                                                                <span class="fa fa-print"></span> Print
                                                            </a>
                                                            @if($intersite->status == 0)
                                                                <form action="{{ route('intersite.post', $intersite->id) }}" method="post" onsubmit="return confirm('Are you sure you want post this invoice?')">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fa fa-check" aria-hidden="true"></i> Post
                                                                    </button>
                                                                </form>
                                                                <a class="dropdown-item" href="{{ route('intersite.edit', $intersite->id) }}">
                                                                    <span class="fa fa-pencil"></span> Edit
                                                                </a>
                                                                <form onsubmit="return confirm('Are you sure you want to delete this intersite ?')"
                                                                      action="{{ route('intersite.delete', $intersite->id) }}" method="post">
                                                                    {{ csrf_field() }}
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="text-danger fa fa-remove"></i> Delete
                                                                    </button>
                                                                </form>
                                                            @endif





                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @endisset
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
    <!-- DataTables -->
    <script src="{{ asset('assets/backend/plugins/datatables/datatables.js') }}"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#record1").DataTable();
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
