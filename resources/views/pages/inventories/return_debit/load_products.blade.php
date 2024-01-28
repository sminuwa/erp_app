<div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
    <div class="c-thead">
        <div class="c-tr">
            <div class="c-h-cell">S.N</div>
            <div class="c-h-cell">Item</div>
            <div class="c-h-cell">Price</div>
            <div class="c-h-cell">Qty</div>
            <div class="c-h-cell">Total</div>
            {{-- <div class="c-h-cell"><span class="ion-ios-trash"></span></div> --}}
        </div>
    </div>
    <div class="c-tbody">

        @foreach (\Cart::getContent() as $item)
            <form class="c-tr ajax-update-input" action="{{ route('ajax.cart.update', $item->id) }}" method="get"
                id="p{{ $item->id }}" data-value="p{{ $item->id }}">
                @csrf
                @php $attr = $item->attributes; @endphp

                <input type="hidden" name="id" class="form-control" value="{{ $item->id }}">

                <div class="c-cell">{{ $loop->iteration }}</div>
                <div class="c-cell text-left">{{ $attr->code ?? null }} - {{ $item->name }}</div>
                <div class="c-cell">
                    <span style="color: red;" id="valid_price{{ $item->id }}"></span>
                    <input type="text" name="unit_price" id="price{{ $item->id }}" class="form-control price"
                        style="min-width:65px;" value="{{ $item->price }}" data-value="p{{ $item->id }}"
                        required />
                </div>
                <div class="c-cell">
                    <span style="color: red;" id="valid_quantity{{ $item->id }}"></span>
                    <input type="text" name="quantity" id="quantity{{ $item->id }}"
                        class="form-control quantity" data-value="p{{ $item->id }}" style="min-width:58px;"
                        value="{{ $item->quantity }}" min="1" required>
                </div>
                <div class="c-cell">
                    <span
                        class="subtotal{{ $item->id }}">{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
                <input name="type" type="hidden" value="returndebit">
                {{-- <div class="c-cell">
                    <a url="{{ route('ajax.cart.delete', $item->id) }}" class="btn btn-danger btn-sm deleteCartItem">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>
                </div> --}}
            </form>
        @endforeach
        <div class="c-tr">
            <div class="c-cell">
            </div>
            <div class="c-cell">
                <span style="font-size:16px;">
                    <small>Total:</small>
                    <span class="totalCart">{{ currency_sign() . number_format(Cart::getTotal(), 2) }}</span> <br>
                </span>
            </div>
        </div>

    </div>

</div>
<form action="{{ isset($operation) ? route('return.debit.update', $purchase->id) : route('return.debit.store') }}"
    method="POST">
    @csrf
    @php
        $date = new DateTime(date('Y-m-d'));
    @endphp
    @isset($purchase->date)
        @php $date = new DateTime($purchase->date); @endphp
    @endisset

    <input type="hidden" name="purchase_id" id="purchase_id" value="{{ $purchase->id }}" />
    <input type="date" class="form-control" name="date" id="date" placeholder="R&D Date"
        value="{{ $date->format('d/m/Y') ?? '' }}" />
    <textarea name="comment" rows="5" cols="100" placeholder="Description" class="form-control">
@isset($operation)
{{ $purchase->comment }}
@endisset
</textarea>
    <div class="form-group text-right">
        <input type="submit" name="Submit" class=" btn btn-primary" value="Submit" />
    </div>
    @isset($operation)
        @method('PUT')
    @endisset
</form>
