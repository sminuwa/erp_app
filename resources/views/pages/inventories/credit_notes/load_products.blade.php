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

        {{-- @if (Cart::getTotal() < 1)
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
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart_products as $product)
                        <tr class="item{{ $product->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $product->attributes['code'] }}</td>
                            <td class="text-left">{{ $product->name }}</td>
                            <td class="text-left">{{ $product->attributes['unit'] }}</td>
                            <td class="text-left">{{ $product->attributes['sold_price'] }}</td>

                            @can('credit.note.cart.update')
                                <form action="{{ route('credit.note.cart.update') }}" method="post"
                                    id="p{{ $product->id }}">
                                    @csrf
                                    <td>
                                        <input type="hidden" name="sold_price" id="price{{ $product->id }}"
                                            class="form-control" style="min-width:65px;"
                                            onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                            value="{{ $product->price }}" data-val="{{ $product->price }}"
                                            data-value="p{{ $product->id }}">
                                        <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                                        <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                            class="form-control quantity" data-value="p{{ $product->id }}"
                                            style="min-width:58px;" value="{{ $product->quantity }}" min="1"
                                            max-qty="{{ $product->quantity }}" required>
                                    </td>
                                    <td>
                                        <span
                                            class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                                    </td>
                                    <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                                    <input type="hidden" name="selling_price" class="form-control"
                                        value="{{ $product->attributes['selling_price'] }}">
                                    <input type="hidden" name="cost_price" class="form-control"
                                        value="{{ $product->attributes['cost_price'] }}">
                                    <input type="hidden" name="unit" class="form-control"
                                        value="{{ $product->attributes['unit'] }}">
                                </form>
                            @endcan
                            <td>
                                @can('credit.note.cart.remove')
                                    <form class="deleteForm" id="delete-form-{{ $product->id }}"
                                        action="{{ route('credit.note.cart.remove', $product->id) }}" method="post"
                                        data-val="{{ $product->id }}">
                                        <input type="hidden" name="order" id="order" value="{{ $order->id }}" />
                                        @csrf
                                        <button class="btn btn-danger btn-sm delete" type="submit">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div class="alert alert-success">
            Total : &#8358; <span class="total">{{ number_format(Cart::getTotal()) }}</span>
        </div> --}}

        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">Store</div>
                    <div class="c-h-cell">Code</div>
                    <div class="c-h-cell">Unit</div>
                    <div class="c-h-cell">Price</div>
                    <div class="c-h-cell">Qty</div>
                    <div class="c-h-cell">Total</div>
                    <div class="c-h-cell"></div>
                </div>
            </div>
            <div class="c-tbody">
                @foreach (\Cart::getContent() as $product)
                    <form class="c-tr" action="{{ route('ajax.cart.update', $product->id) }}" method="get"
                        id="p{{ $product->id }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="selling_price" value="{{ $product->attributes['selling_price'] }}">
                        <input type="hidden" name="cost_price" value="{{ $product->attributes['cost_price'] }}">
                        <input type="hidden" name="qty_available" value="{{ $product->attributes['qty_available'] }}">
                        <input type="hidden" name="qty_available" value="{{ $product->attributes['qty_available'] }}">
                        <input type="hidden" name="store" value="{{ $product->attributes['store'] }}">
                        <input type="hidden" name="code" value="{{ $product->attributes['code'] }}">

                        <div class="c-cell" class="text-left">{{ $product->attributes['store'] }}</div>
                        <div class="c-cell" class="text-left">{{ $product->attributes['code'] }} -{{ $product->name }}
                        </div>
                        <div class="c-cell">
                            <?php
                            $units = \App\Models\ProductUnitMeasure::join('products', 'products.id', 'product_unit_measures.product_id')
                                ->where('products.code', $product->attributes['code'])
                                ->select('product_unit_measures.*')
                                ->get();
                            ?>
                            @csrf
                            <select name="unit" id="unit{{ $product->id }}" class="form-control unit_measure"
                                data-value="p{{ $product->id }}" style="min-width:65px;">
                                <option>{{ $product->attributes['unit'] }}</option>
                                @foreach ($units as $unit)
                                    <option>{{ $unit->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="c-cell">
                            <input readonly="readonly"  type="text" name="sold_price" id="price{{ $product->id }}"
                                class="form-control price" style="min-width:65px;"
                                @if ($type == 'invoice') onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))" @endif
                                value="{{ $product->price }}" data-val="{{ $product->attributes['selling_price'] }}"
                                min="1" data-value="p{{ $product->id }}">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                        </div>
                        <div class="c-cell">
                            <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                class="form-control quantity" data-value="p{{ $product->id }}" style="min-width:58px;"
                                value="{{ $product->quantity }}" min="1"
                                @if ($type == 'invoice') max-qty="{{ $product->attributes['qty_available'] }}" @endif
                                required>
                            <span style="color: red;" id="valid_qty{{ $product->id }}"></span>
                        </div>
                        <div class="c-cell">

                            <span class="subtotal{{ $product->id }}">{{ number_format(floatVal(str_replace(',', '', $product->price)) * $product->quantity, 2) }}</span>
                        </div>
                        <div class="c-cell">
                            <a url="{{ route('ajax.cart.delete', $product->id) }}"
                                class="btn btn-danger btn-sm deleteCartItem">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
        <div class="alert alert-success">
            Total : &#8358; <span class="totalCart">{{ number_format(\Cart::getTotal(), 2) }}</span>
        </div>
        <form action="{{ route('credit.note.store') }}" method="POST">
            @csrf
            <input type="hidden" name="date" class="date" value="" />
            <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}" />
            <input type="hidden" name="customer_id" id="customer_id" value="{{ $order->customer_id }}" />
            <input name="comment" placeholder="Comment" class="form-control">

            <div class="form-group text-right mt-3">
                <input type="submit" onclick="$('.date').val($('.date_').val())" class=" btn btn-primary"
                    value="Submit" />
            </div>

        </form>
    </div>
    <!-- /.card-body -->
</div>
