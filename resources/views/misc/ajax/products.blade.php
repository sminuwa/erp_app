<option value="">Select...</option>
@foreach ($records as $record)
    <option value="{{ $record->id }}">
        {{ $record->code }} - {{ $record->name }}
    </option>
@endforeach

