@extends('../layout/' . $layout)

@section('subhead')
    <title>Roles </title>
@endsection
@section('subcontent')
    <h2><span data-lucide="eye" class="text-primary"></span> {{ $record->name }}</h2>
    <div class="btn-group">
        <a class="btn btn-secondary" href="{{ route('roles.index') }}">
            <span data-lucide="list" class="text-primary"></span>
        </a>
        <a class="btn btn-secondary" href="{{ route('roles.create') }}">
            <span data-lucide="plus-circle" class="text-success"></span>
        </a>
        <a class="btn btn-secondary" href="{{ route('roles.edit', $record->id) }}">
            <span data-lucide="edit-2" class="text-info"></span>
        </a>
        <form onsubmit="return confirm('Are you sure you want to delete?')"
            action="{{ route('roles.destroy', $record->id) }}" method="post" style="display: inline">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-secondary cursor-pointer">
                <i data-lucide="trash-2" class="text-danger"></i>
            </button>
        </form>

    </div>
    <div class="row">
        <div class="col-sm-4">
            @include('cards.role')
        </div>
    </div>
@endSection
