<div class="row">
    <div class="col-sm-4">
        <div class="card">
            <div class="card-header">
                Products
            </div>
            <div class="card-body">
                <form action="{{ route('purchases.cart.store') }}" method="POST">
                    <input type="hidden" name="purchase_id" value="{{ $model->id }}" />
                    <input type="hidden" name="type" value="{{ $type }}" />
                    @csrf
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select type="number"
                            class="form-control select2-single ajax-categories  {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                            name="category_id" id="category_id" required="required"></select>
                        @if ($errors->has('category_id'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('category_id') }}</strong>
                            </div>
                        @endif
                    </div>


                    <div class="form-group">
                        <label for="product_id">Product Name</label>
                        <select
                            class="form-control select2-single ajax-products {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                            name="product_id" id="product_id" required="required">
                            <option value="">Select...</option>
                            @if (isset($products))
                                @if (old('category_id', $model->category_id))
                                    @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                            {{ $data->name }}</option>
                                    @endforeach
                                @else
                                    @foreach ($products as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                            {{ $data->name }}</option>
                                    @endforeach
                                @endif
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="qty_supplied">Quantity Supplied</label>
                        <input type="number"
                            class="form-control {{ $errors->has('qty_supplied') ? ' is-invalid' : '' }}"
                            name="qty_supplied" id="qty_supplied" placeholder="" required="required">
                        @if ($errors->has('qty_supplied'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('qty_supplied') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="unit_price">Cost Price</label>
                        <input type="text"
                            class="form-control {{ $errors->has('unit_price') ? ' is-invalid' : '' }}"
                            name="unit_price" id="unit_price" placeholder="" required="required">
                        @if ($errors->has('unit_price'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('unit_price') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group text-right ">
                        <button type="submit" class="btn btn-primary"><span class="ion-android-cart"> </span>Add to
                            Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="card">
            <div class="card-header">
                <i class="ion-android-cart"></i> Supplier Cart: <small>Purchased Products</small>
            </div>
            <div class="card-body table-responsive">
                @if (Cart::getTotal() < 1)
                    <div class="alert alert-danger">
                        No Product Added
                    </div>
                @else
                    <table class="table table-bordered table-striped text-center mb-3">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th>Sub Total</th>
                                <th><span class="ion-ios-trash"></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($cart_products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-left">{{ $product->name }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ number_format($product->quantity, 0, '', ',') }}</td>
                                    <td style="text-align: right">
                                        {{ number_format($product->price * $product->quantity, 2) }}
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="deleteItem({{ $product->id }})">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                        <form id="delete-form-{{ $product->id }}"
                                            action="{{ route('purchases.cart.remove', $product->id) }}" method="post"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach --}}
                            @foreach ($cart_products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-left">{{ $product->name }}</td>


                                    <form action="{{ route('purchase.cart.update') }}" method="post"
                                        id="p{{ $product->id }}">
                                        @csrf
                                        @method('PUT')
                                        <td>
                                            <input type="text" name="cost_price" id="price{{ $product->id }}"
                                                class="form-control price" style="min-width:65px;"
                                                onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                                                value="{{ $product->price }}" data-val="{{ $product->price }}"
                                                data-value="p{{ $product->id }}">
                                        </td>
                                        <td>
                                            <input type="text" name="quantity" id="quantity{{ $product->id }}"
                                                class="form-control quantity" data-value="p{{ $product->id }}"
                                                style="min-width:58px;" value="{{ $product->quantity }}" min="1"
                                                required>
                                        </td>
                                        <td><span
                                                class="subtotal{{ $product->id }}">{{ number_format($product->price * $product->quantity, 2) }}</span>
                                        </td>
                                        <input type="hidden" name="id" class="form-control"
                                            value="{{ $product->id }}">

                                    </form>

                                    <td>
                                        {{-- <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                        </button> --}}
                                        <button class="btn btn-danger btn-sm" type="button"
                                            onclick="deleteItem({{ $product->id }})">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                        <form id="delete-form-{{ $product->id }}"
                                            action="{{ route('cart.remove', $product->id) }}" method="post"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <div class="alert alert-success" id="total">
                    Total : <span id="total">{{ number_format(Cart::getTotal()) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="card">
            <div class="card-header">
                Purchase Details
            </div>
            <div class="card-body">
                <h5 class="card-title"></h5>
                <form action="{{ isset($route) ? $route : route('purchases.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

                    <div class="form-group">
                        <label for="supplier_id">Supplier Name</label>
                        <select
                            class="form-control select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                            name="supplier_id" id="supplier_id" required="required">
                            <option value="">Select...</option>
                            @if (isset($suppliers))
                                @foreach ($suppliers as $data)
                                    <option value="{{ $data->id }}"
                                        {{ $data->id == $model->supplier_id ? 'selected' : '' }}>
                                        {{ $data->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="invoice">Reference No</label>
                        <input type="hidden"
                            class="form-control {{ $errors->has('old_invoice') ? ' is-invalid' : '' }}"
                            name="old_invoice" id="old_invoice" value="{{ old('old_invoice', $model->invoice) }}">
                        <input type="text" class="form-control {{ $errors->has('invoice') ? ' is-invalid' : '' }}"
                            name="invoice" id="invoice" value="{{ old('invoice', $model->invoice) }}"
                            placeholder="" maxlength="191" required="required">
                        @if ($errors->has('invoice'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('invoice') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="purchase_date">Purchase Date</label>
                        <div class="input-group">
                            <input type="text"
                                class="form-control datepicker {{ $errors->has('purchase_date') ? ' is-invalid' : '' }}"
                                name="purchase_date" id="purchase_date"
                                value="{{ $model->purchase_date == null ? date('Y-m-d') : old('purchase_date', $model->purchase_date->format('Y-m-d')) }}"
                                placeholder="" required="required">
                            <?php date_default_timezone_set('Africa/Lagos'); ?>
                            <input type="text"
                                class="form-control datetimepicker input-sm {{ $errors->has('purchase_time') ? ' is-invalid' : '' }}"
                                name="purchase_time" id="purchase_time"
                                value="{{ $model->purchase_time == null ? date('h:i') : old('purchase_time', $model->purchase_time) }}"
                                placeholder="Time" required="required">
                            <div class="input-group-addon">
                                <label for="purchase_date" class="fa fa-calendar">
                                </label>
                            </div>
                        </div>
                        @if ($errors->has('purchase_date'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('purchase_date') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="vehicle_reg_no">Truck No</label>
                        <input type="text"
                            class="form-control {{ $errors->has('vehicle_reg_no') ? ' is-invalid' : '' }}"
                            name="vehicle_reg_no" id="vehicle_reg_no"
                            value="{{ old('vehicle_reg_no', $model->vehicle_reg_no) }}" placeholder=""
                            maxlength="191">
                        @if ($errors->has('vehicle_reg_no'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('vehicle_reg_no') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="source_store_id">Store</label>
                        <select
                            class="form-control select2-single {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
                            name="source_store_id" id="source_store_id" required="required">
                            @if (isset($stores))
                                <option value="">Select...</option>
                                @foreach ($stores as $data)
                                    <option value="{{ $data->id }}"
                                        {{ $data->id == $model->source_store_id ? 'selected' : '' }}>
                                        {{ $data->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
            </div>
            <input type="hidden" name="updated_by" value="{{ Auth::id() }}" />
            <div class="form-group text-right ">
                <input type="submit" class="btn btn-primary" value="Save" />
            </div>
            </form>
        </div>
    </div>
</div>

<div class="row table-responsive">

</div>
</div>
