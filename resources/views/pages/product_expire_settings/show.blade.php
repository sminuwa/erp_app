@extends('layouts.backend.app')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{route('product_expire_settings.index')}}">product_expire_settings</a>
</li>
<li class="breadcrumb-item">
    {{$record->id}}
</li>

@endsection
@section('header')
<h3><i class="fa fa-eye"></i> {{$record->id}}</h3>
@endsection
@section('tools')
<div class="btn-group">
    
<a class="btn btn-secondary" href="{{route('product_expire_settings.create')}}">
    <span class="fa fa-plus"></span>
</a>
<a class="btn btn-secondary" href="{{route('product_expire_settings.edit',$record->id)}}">
    <span class="fa fa-pencil"></span>
</a>
<form onsubmit="return confirm('Are you sure you want to delete?')"
      action="{{route('product_expire_settings.destroy',$record->id)}}"
      method="post"
      style="display: inline">
    {{csrf_field()}}
    {{method_field('DELETE')}}
    <button type="submit" class="btn btn-secondary cursor-pointer">
        <i class="text-danger fa fa-remove"></i>
    </button>
</form>

</div>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-4">
        @include('cards.product_expire_setting')
    </div>
</div>
@endSection