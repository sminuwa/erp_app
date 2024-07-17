@if (count(\Cart::getContent()) < 1)
    <div class="alert alert-danger">
        No Product Added
    </div>
@else
    @if ($type == 'order' || $type == 'proforma' || $type == 'invoice')
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
    @endif
    @if ($type == 'interstore')
        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">S.N</div>
                    <div class="c-h-cell">Code</div>
                    <div class="c-h-cell">Name</div>
                    <div class="c-h-cell">Qty</div>
                    <div class="c-h-cell">Source Store</div>
                    <div class="c-h-cell">Destination Store</div>
                    <div class="c-h-cell"></div>
                </div>
            </div>
            <div class="c-tbody">
                @foreach (\Cart::getContent() as $product)
                    <form class="c-tr" action="{{ route('ajax.cart.update', $product->id) }}" method="get"
                        id="p{{ $product->id }}">
                        @csrf
                        @php $attr = $product->attributes @endphp
                        <div class="c-cell">{{ $loop->iteration }}</div>
                        <div class="c-cell" class="text-left">{{ $product->attributes['code'] }}</div>
                        <div class="c-cell" class="text-left">{{ $product->name }}</div>
                        <div class="c-cell">{{ $product->quantity }}</div>
                        <div class="c-cell">{{ \App\Models\Store::find($attr['source_store_id'])->name }}</div>
                        <div class="c-cell">{{ \App\Models\Store::find($attr['destination_store_id'])->name }}</div>
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
    @endif
    @if ($type == 'intersite')
        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">S.N</div>
                    <div class="c-h-cell">Code</div>
                    <div class="c-h-cell">Item</div>
                    <div class="c-h-cell">Qty</div>
                    <div class="c-h-cell">Source Store</div>
                    <div class="c-h-cell"></div>
                </div>
            </div>
            <div class="c-tbody">
                @foreach (\Cart::getContent() as $product)
                    <form class="c-tr" action="{{ route('ajax.cart.update', $product->id) }}" method="get"
                        id="p{{ $product->id }}">
                        @csrf
                        <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                        <input type="hidden" name="cost_price" class="form-control"
                            value="{{ $product->price ?? '' }}">
                        <input type="hidden" name="unit" id="unit{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['unit'] ?? '' }}">
                        <input type="hidden" name="code" id="code{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['code'] ?? '' }}">
                        <input type="hidden" name="store_id" id="store_id{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['store_id'] ?? '' }}">
                        <input type="hidden" name="product_id" id="product_id{{ $product->id }}"
                            class="form-control" value="{{ $product->attributes['product_id'] ?? '' }}">
                        @php $attr = $product->attributes @endphp
                        <div class="c-cell">{{ $loop->iteration }}</div>
                        <div class="c-cell" class="text-left">{{ $product->attributes['code'] }}</div>
                        <div class="c-cell" class="text-left">{{ $product->name }}</div>
                        <div class="c-cell" class="text-right">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                            <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                class="form-control quantity" data-value="p{{ $product->id }}"
                                style="min-width:58px;" value="{{ $product->quantity }}" min="1" required>

                        </div>
                        <div class="c-cell">{{ \App\Models\Store::find($attr['store_id'])->name }}</div>
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
    @endif
    @if ($type == 'grn')
        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">S.N</div>
                    <div class="c-h-cell">Store</div>
                    <div class="c-h-cell">Code</div>
                    <div class="c-h-cell">Item</div>
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
                        <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                        <input type="hidden" name="cost_price" class="form-control"
                            value="{{ $product->attributes['cost_price'] ?? '' }}">
                        <input type="hidden" name="unit" id="unit{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['unit'] ?? '' }}">
                        <input type="hidden" name="code" id="code{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['code'] ?? '' }}">
                        <input type="hidden" name="store_id" id="store_id{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['store_id'] ?? '' }}">
                        <div class="c-cell">{{ $loop->iteration }}</div>
                        <div class="c-cell" class="text-left">
                            <select type="text" name="store_code" id="store_code{{ $product->id }}"
                                class="form-control store_code" data-value="p{{ $product->id }}"
                                style="min-width:58px;"
                                onchange="$('#store_id{{ $product->id }}').val($(this).val())" required>
                                <option value="">select..</option>
                                <?php $stores = \App\Models\Store::where('branch_id', auth()->user()->branch->id)->get(); ?>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}"
                                        {{ $product->attributes['store_id'] == $store->id ? 'selected' : '' }}>
                                        {{ $store->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="c-cell" class="text-left">{{ $product->attributes['code'] ?? '' }}</div>
                        <div class="c-cell" class="text-left">{{ $product->name }}</div>
                        <div class="c-cell" class="text-left">{{ $product->attributes['unit'] ?? '' }}</div>
                        <div class="c-cell" class="text-left">
                            <input type="text" name="price" id="price{{ $product->id }}"
                                class="form-control price" style="min-width:65px;"
                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                value="{{ $product->price }}" data-val="{{ $product->price }}"
                                data-value="p{{ $product->id }}">
                        </div>
                        <div class="c-cell">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                            <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                class="form-control quantity" data-value="p{{ $product->id }}"
                                style="min-width:58px;" value="{{ $product->quantity }}" min="1" required>
                        </div>
                        <div class="c-cell">
                            <span
                                class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
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
    @endif
    @if ($type == 'request')
        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">S.N</div>
                    <div class="c-h-cell">Code</div>
                    <div class="c-h-cell">Item</div>
                    <div class="c-h-cell">Unit</div>
                    <div class="c-h-cell">Price</div>
                    <div class="c-h-cell">Qty</div>
                    <div class="c-h-cell">Total</div>
                </div>
            </div>
            <div class="c-tbody">
                @foreach (\Cart::getContent() as $product)
                    <form class="c-tr" action="{{ route('ajax.cart.update', $product->id) }}" method="get"
                        id="p{{ $product->id }}">
                        @csrf
                        <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                        <input type="hidden" name="cost_price" class="form-control"
                            value="{{ $product->attributes['cost_price'] ?? '' }}">
                        <input type="hidden" name="unit" id="unit{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['unit'] ?? '' }}">
                        <input type="hidden" name="code" id="code{{ $product->id }}" class="form-control"
                            value="{{ $product->attributes['code'] ?? '' }}">
                        <input type="hidden" name="store_id" id="store_id{{ $product->id }}"
                            class="form-control" value="{{ $product->attributes['store_id'] ?? '' }}">
                        <div class="c-cell">{{ $loop->iteration }}</div>
                        <div class="c-cell text-left">{{ $product->attributes['code'] ?? '' }}</div>
                        <div class="c-cell text-left">{{ $product->name }}</div>
                        <div class="c-cell text-left">{{ $product->attributes['unit'] ?? '' }}</div>
                        <div class="c-cell text-left">
                            <input type="text" name="price" id="price{{ $product->id }}"
                                class="form-control price" style="min-width:65px;"
                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                value="{{ $product->price }}" data-val="{{ $product->price }}"
                                data-value="p{{ $product->id }}">
                        </div>
                        <div class="c-cell">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                            <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                class="form-control quantity" data-value="p{{ $product->id }}"
                                style="min-width:58px;" value="{{ $product->quantity }}" min="1" required>
                        </div>
                        <div class="c-cell"><span
                                class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
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
    @endif
    @if ($type == 'adjustment')
        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">S.N</div>
                    <div class="c-h-cell">Store</div>
                    <div class="c-h-cell">Name</div>
                    <div class="c-h-cell">Quantity</div>
                    <div class="c-h-cell">Expiry Date</div>
                    <div class="c-h-cell"><span class="ion-ios-trash"></span></div>
                </div>
            </div>
            <div class="c-tbody">
                @foreach (\Cart::getContent() as $product)
                    <form class="c-tr" action="{{ route('ajax.cart.update', $product->id) }}" method="get"
                        id="p{{ $product->id }}">
                        @csrf
                        @php $attr = $product->attributes; @endphp
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="hidden" name="product_id" value="{{ $attr->product_id }}">
                        <input type="hidden" name="store_id" value="{{ $attr->store_id }}">
                        <input type="hidden" name="expiry_date" value="{{ $attr->expiry_date }}">
                        <input type="hidden" name="operation" class="form-control operation"
                            value="{{ $attr->operation }}">
                        <div class="c-cell">{{ $loop->iteration }}</div>
                        <div class="c-cell">{{ \App\Models\Store::find($attr->store_id)->code ?? null }}</div>
                        <div class="c-cell text-left">{{ $product->attributes['code'] ?? null }} -
                            {{ $product->name }}</div>
                        <div class="c-cell">
                            <span style="color: red;" id="valid_price{{ $product->id }}"></span>
                            <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                class="form-control quantity" data-value="p{{ $product->id }}"
                                style="min-width:58px;" value="{{ $product->quantity }}" min="1" required>
                        </div>
                        <div class="c-cell text-left">{{ $product->attributes['expiry_date'] ?? null }}</div>
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
    @endif
    @if ($type == 'journal')
        <div class="c-table c-table-bordered c-table-striped" style="font-size: 12px; ">
            <div class="c-thead">
                <div class="c-tr">
                    <div class="c-h-cell">S.N</div>
                    <div class="c-h-cell">Branch</div>
                    <div class="c-h-cell">Account</div>
                    <div class="c-h-cell">Credit</div>
                    <div class="c-h-cell">Debit</div>
                    <div class="c-h-cell">Description</div>
                    <div class="c-h-cell"><span class="ion-ios-trash"></span></div>
                </div>
            </div>
            <div class="c-tbody">
                @php
                    $total_credit = 0;
                    $total_debit = 0;
                @endphp
                @foreach (\Cart::getContent() as $item)
                    <form class="c-tr ajax-update-input" action="{{ route('ajax.cart.update', $item->id) }}"
                        method="get" id="p{{ $item->id }}" data-value="p{{ $item->id }}">
                        @csrf
                        @php $attr = $item->attributes; @endphp
                        @isset($journal)
                            <input type="hidden" name="journal_id" value="{{ $journal->id }}">
                        @endisset
                        <input type="hidden" name="f{{ $item->id }}" value="{{ $item->id }}">
                        <input type="hidden" name="branch_id" value="{{ $item->attributes['branch_id'] }}">
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <input type="hidden" name="payer_id" value="{{ $attr->payer_id }}">
                        <input type="hidden" name="account_type" value="{{ $attr->account_type }}">
                        <input type="hidden" name="credit" value="{{ $attr->credit }}">
                        <input type="hidden" name="debit" value="{{ $attr->debit }}">
                        <div class="c-cell">{{ $loop->iteration }}</div>
                        <div class="c-cell text-left">{{ $attr->branch_code ?? null }} </div>
                        <div class="c-cell text-left">{{ $attr->code ?? null }} - {{ $item->name }}</div>
                        <div class="c-cell">
                            <span style="color: red;" id="valid_credit{{ $item->id }}"></span>
                            <input type="text" name="credit" id="credit{{ $item->id }}"
                                class="form-control credit ajax-update-input" data-value="p{{ $item->id }}"
                                style="min-width:58px;" value="{{ number_format($attr->credit, 2) }}"
                                required>
                        </div>
                        <div class="c-cell">
                            <span style="color: red;" id="valid_debit{{ $item->id }}"></span>
                            <input type="text" name="debit" id="debit{{ $item->id }}"
                                class="form-control debit ajax-update-input" data-value="p{{ $item->id }}"
                                style="min-width:58px;" value="{{ number_format($attr->debit, 2) }}"
                                required>
                        </div>
                        <div class="c-cell">
                            <span style="color: red;" id="valid_desc{{ $item->id }}"></span>
                            <input type="text" name="description" id="description{{ $item->id }}"
                                class="form-control description ajax-update-input" data-value="p{{ $item->id }}"
                                style="min-width:58px;" value="{{ $attr->description }}">
                        </div>
                        <div class="c-cell">
                            <a url="{{ route('ajax.cart.delete', $item->id) }}"
                                class="btn btn-danger btn-sm deleteCartItem">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </div>
                    </form>


                    @php
                        $total_credit += $attr->credit;
                        $total_debit += $attr->debit;
                    @endphp
                @endforeach
                <div class="c-tr">
                    <div class="c-cell">
                    </div>
                    <div class="c-cell">
                        <span style="font-size:16px;">
                            <small>Total Credit:</small>
                            <span id="total_credit">{{ currency_sign() . number_format($total_credit, 2) }}</span> <br>
                            <small>Total Debit:</small>
                            <span id="total_debit">{{ currency_sign() . number_format($total_debit, 2) }}</span> <br>
                            <small>Balance:</small>
                            <span id="total_balance">{{ currency_sign() . number_format($total_credit - $total_debit, 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
