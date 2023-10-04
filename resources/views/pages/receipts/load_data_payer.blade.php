<option value="">Select...</option>
@foreach ($payers as $payer)
    <option value="{{$payer->id}}">{{$payer->code}}-{{$payer->name}}</option>
@endforeach