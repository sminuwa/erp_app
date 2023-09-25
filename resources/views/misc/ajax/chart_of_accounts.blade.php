<option value="">Select...</option>
@foreach ($records as $record)
    <option value="{{ $record->class }}">
        {{ $record->class }} - {{ $record->description }}
    </option>
@endforeach

