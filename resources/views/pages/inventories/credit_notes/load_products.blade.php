<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-info"></i>
            Item Lists for Invoice
            @if (isset($order))
                {{ $order->reference }}
            @elseif (Cart::getTotal() > 0)
                {{ $reference }}
            @endif

        </h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive">

        @if (Cart::getTotal() < 1)
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
                    @foreach ($cart_products as $product)
                        <tr class="item{{ $product->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $product->code }}</td>
                            <td class="text-left">{{ $product->name }}</td>
                            <td class="text-left">{{ $product->attributes['unit'] }}</td>
                            <td class="text-left">{{ $product->attributes['sold_price'] }}</td>

                            <form action="{{ route('credit.note.cart.update') }}" method="post" id="p{{ $product->id }}">
                                @csrf
                                <td>
                                    <input type="hidden" name="sold_price"
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
                                        max-qty="{{ $product->quantity }}"
                                        required>
                                </td>
                                <td>
                                    <span class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                                </td>
                                <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                                <input type="hidden" name="selling_price" class="form-control"
                                    value="{{ $product->attributes['selling_price'] }}">
                                <input type="hidden" name="cost_price" class="form-control"
                                    value="{{ $product->attributes['cost_price'] }}">
                                <input type="hidden" name="unit" class="form-control"
                                       value="{{ $product->attributes['unit'] }}">
                            </form>

                            <td>
                                <form class="deleteForm" id="delete-form-{{ $product->id }}"
                                    action="{{ route('credit.note.cart.remove', $product->id) }}" method="post"
                                    data-val="{{ $product->id }}"
                                    >
                                    <input type="hidden" name="order" id="order" value="{{ $order->id }}" />
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
            Total : &#8358; <span class="total">{{ number_format(Cart::getTotal()) }}</span>
        </div>
        <form action="{{ route('credit.note.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" class="date" value="" />
            <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}" />
            <input type="hidden" name="customer_id" id="customer_id" value="{{ $order->customer_id }}" />
            <input name="comment" placeholder="Comment" class="form-control">

            <div class="form-group text-right mt-3">
                <input type="submit" onclick="$('.date').val($('.date_').val())" class=" btn btn-primary" value="Submit" />
            </div>

        </form>
    </div>
    <!-- /.card-body -->
</div>
