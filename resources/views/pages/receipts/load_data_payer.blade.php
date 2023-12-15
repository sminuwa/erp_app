<option value="">Select...</option>
@foreach ($payers as $payer)
    <option value="{{$payer->id}}">{{$payer->code ?? ($payer->number ?? null)}} - {{$payer->name ?? ($payer->description ?? null)}}</option>
@endforeach
