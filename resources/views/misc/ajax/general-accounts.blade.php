<option value="">Select...</option>
@foreach ($records as $record)
    <option value="{{ $record->id }}">
        {{ $record->number }} - {{ $record->description }}
    </option>
@endforeach

