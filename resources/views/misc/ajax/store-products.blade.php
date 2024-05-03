<option value="">Select...</option>
@foreach ($records as $record)
    <option value="{{ $record->product_id }}">
        {{ $record->product->code }} - {{ $record->product->name }}
    </option>
@endforeach

