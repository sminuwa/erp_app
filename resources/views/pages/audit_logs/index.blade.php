@extends('layouts.app')
@section('breadcrumb')
<li class="breadcrumb-item">
    audit_logs
</li>
@endsection
@section('header')
<h3><i class="fa fa-list"></i> audit_logs </h3>
@endsection
@section('tools')
<a class="btn btn-secondary" href="{{route('audit_logs.create')}}">
    <span class="fa fa-plus"></span>
</a>
@endsection

@section('content')
<div class="row">
    @foreach($records as $record)
    <div class="col-sm-6">
        @include('cards.audit_log')
    </div>
    @endforeach
</div>
{!! $records->render() !!}
@endSection