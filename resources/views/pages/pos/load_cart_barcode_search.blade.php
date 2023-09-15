<table class="table table-bordered table-striped text-center">
    <thead>
        <tr style="font-size: 14px;">
            <th>S.N</th>
            <th style="width:30%">Item Description</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
            {{-- <th><span class="ion-refresh"></span></th> --}}
            <th><span class="ion-ios-trash"></span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cart_products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $product->name }}</td>

                <form action="{{ route('cart.update') }}" method="post" id="p{{ $product->id }}">
                    @csrf
                    @method('PUT')
                    <td>
                        @can('edit.daily.sale')
                            <input type="text" name="sold_price" id="price{{ $product->id }}" class="form-control price"
                                style="min-width:65px;"
                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                value="{{ $product->price }}" data-val="{{ $product->attributes['cost_price'] }}"
                                data-value="p{{ $product->id }}">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                        @else
                            <input type="text" name="sold_price" id="price{{ $product->id }}" class="form-control price"
                                readonly style="min-width:65px;"
                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                value="{{ $product->price }}" data-val="{{ $product->attributes['cost_price'] }}"
                                data-value="p{{ $product->id }}">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                        @endcan

                    </td>
                    <td>
                        <input type="text" name="quantity" id="quantity{{ $product->id }}"
                            class="form-control quantity" data-value="p{{ $product->id }}" style="min-width:58px;"
                            value="{{ $product->quantity }}" min="1"
                            max-qty="{{ $product->attributes['qty_available'] }}" required>
                        <span style="color: red;" id="valid_qty{{ $product->id }}"></span>
                    </td>
                    <td><span
                            class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                    </td>
                    <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                    <input type="hidden" name="selling_price" class="form-control"
                        value="{{ $product->attributes['selling_price'] }}">
                    <input type="hidden" name="cost_price" class="form-control"
                        value="{{ $product->attributes['cost_price'] }}">
                    <input type="hidden" name="qty_available" class="form-control"
                        value="{{ $product->attributes['qty_available'] }}">
                    {{-- <td>
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                        </button>
                    </td> --}}
                </form>

                <td>
                    <button class="btn btn-danger btn-sm" type="button" onclick="deleteItem({{ $product->id }})">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                    <form id="delete-form-{{ $product->id }}" action="{{ route('cart.remove', $product->id) }}"
                        method="post" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="alert alert-info">
    {{-- <p>Quantity : {{ Cart::getTotalQuantity() }}</p> --}}
    <p>Sub Total : &#8358; <span id="subtotal">{{ number_format(Cart::getSubTotal(), 2) }}</span></p>
</div>
<div class="alert alert-success">
    Total : &#8358; <span id="total">{{ number_format(Cart::getTotal()) }}</span>
</div>
