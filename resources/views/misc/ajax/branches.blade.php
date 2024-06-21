<option value="">Select branch...</option>
@foreach ($records as $record)
    <option value="{{ $record->id }}">
        {{ $record->code }} - {{ $record->name }}
    </option>
@endforeach

