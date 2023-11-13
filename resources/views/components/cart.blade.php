@if (\Cart::getTotal() < 1)
    <div class="alert alert-danger">
        No Product Added
    </div>
@else
    <table class="table table-bordered table-striped text-center" style="font-size: 12px;">
        <thead>
        <tr>
            <th>S.N</th>
            <th>Code</th>
            <th>Item</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
            <!--<th><span class="ion-refresh"></span></th> -->
            {{-- <th><span class="ion-ios-trash"></span></th> --}}
        </tr>
        </thead>
        <tbody>
        @foreach (\Cart::getContent() as $product)
            <tr class="item{{ $product->id }}">
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $product->attributes['code'] ?? '' }}</td>
                <td class="text-left">{{ $product->name }}</td>
                <td class="text-left">{{ $product->attributes['unit'] ?? '' }}</td>
                <td class="text-left">{{ $product->price ?? 0.0 }}</td>

                <form action="{{ route('ajax.cart.update', $product->id) }}" method="post" id="p{{ $product->id }}">
                    @csrf
                    <td>
                        <input type="hidden" name="price"
                               id="price{{ $product->id }}" class="form-control"
                               style="min-width:65px;"
                               onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                               value="{{ $product->price }}"
                               data-val="{{ $product->price }}"
                               data-value="p{{ $product->id }}">
                        <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                        <input
                            type="text"
                            name="quantity"
                            id="quantity{{ $product->id }}"
                            class="form-control quantity"
                            data-value="p{{ $product->id }}"
                            style="min-width:58px;"
                            value="{{ $product->quantity }}"
                            min="1"
{{--                            max-qty="{{ $product->quantity ? $product->quantity : 10000 }}"--}}
                            required>
                    </td>
                    <td>
                        <span class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                    </td>
                    <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                    <input type="hidden" name="cost_price" class="form-control"
                           value="{{ $product->attributes['cost_price'] ?? '' }}">
                    <input type="hidden" name="unit" id="unit{{ $product->id }}" class="form-control" value="{{ $product->attributes['unit'] ?? '' }}">
                    <input type="hidden" name="code" id="code{{ $product->id }}" class="form-control" value="{{ $product->attributes['code'] ?? '' }}">
                </form>

                <td>
                    <form class="deleteForm deleteCartItem" id="delete-form-{{ $product->id }}"
                          action="{{ route('ajax.cart.delete', $product->id) }}" method="post"
                          data-val="{{ $product->id }}"
                    >
                        @csrf
                        <button class="btn btn-danger btn-sm delete" type="submit"
                        >
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
<div class="alert alert-success">
    Total : &#8358; <span class="totalCart">{{ number_format(\Cart::getTotal()) }}</span>
</div>
