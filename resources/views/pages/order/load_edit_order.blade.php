<div class="card-body table-responsive">
    @foreach ($order->order_items()->where('status',1)->get() as $item)
        <form action="{{ route('orders.update', $item->id) }}" method="post">
            @csrf
            
            <div class="row">
                <div class="form-group col-sm-2 mb-2">
                    <label for="qty" class="col-sm-2 col-form-label">QTY</label>
                    <input type="number" class="form-control form-control-sm" value="{{ $item->quantity }}" name="qty"
                        id="qty{{ $item->id }}">
                </div>
                <div class="form-group col-sm-3 mb-2">
                    <label for="qty" class="col-sm-2 col-form-label">Item</label>
                    <input type="text" readonly class="form-control form-control-sm"
                        value="{{ $item->storeProduct->product->name }}">
                </div>
                <div class="form-group col-sm-3 mb-2">
                    <label for="store_product_id" class="col-sm-2 col-form-label">Store</label>
                    <select class="form-control form-control-sm" name="store_product_id"
                        id="store_product_id{{ $item->id }}">
                        @foreach (App\Models\StoreProduct::where('qty_available', '>', 0)->where('product_id', $item->storeProduct->product_id)->get()
    as $store)
                            <option value="{{ $store->id }}"
                                {{ $item->store_product_id == $store->id ? 'selected' : '' }}>
                                {{ $store->store->name }}({{ $store->qty_available }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-sm-2 mb-2">
                    <label for="new_cost" class="col-sm-2 col-form-label">Cost</label>
                    <input type="number" class="form-control form-control-sm" name="new_cost"
                        id="unit_cost{{ $item->id }}" value="{{ $item->unit_cost }}">
                </div>
                <div class="form-group col-sm-2 mb-2">
                    <label for="unit_cost" class="col-sm-2 col-form-label">Total</label>
                    <input type="number" class="form-control form-control-sm" readonly name="total"
                        value="{{ $item->total }}" />
                </div>
                <div class="form-group col-sm-2 mb-2">
                    <br />
                    <button class="btn btn-danger btn-sm btnForm" type="submit" data-val="{{ $item->id }}">
                        <i class="fa fa-refresh"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" type="button" onclick="deleteItem({{ $item->id }})">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                    <form id="delete-form-{{ $item->id }}"
                        action="{{ route('orders.detail.destroy', $item->id) }}" method="post" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    @method('PUT')
                </div>
            </div>
        </form>
    @endforeach
</div>
