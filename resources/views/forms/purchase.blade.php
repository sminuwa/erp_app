<input name="cart_page_type" type="hidden" value="grn">
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                Purchase Details
            </div>
            <div class="card-body">
                <h5 class="card-title"></h5>

                <form action="{{ isset($route) ? $route : route('purchases.store') }}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="{{ isset($method) ? $method : 'POST' }}" />

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="supplier_id">Supplier Name {{ $model->supplier_id }}</label>
                                <select
                                    class="form-control select2-single {{ $errors->has('supplier_id') ? ' is-invalid' : '' }}"
                                    name="supplier_id" id="supplier_id" required="required" autocomplete="off">
                                    @if(isset($model->id))
                                        <option value="{{ $model->supplier->id }}">{{ $model->supplier->code }} - {{ $model->supplier->name }}</option>
                                    @endif
                                    <option value="">Select...</option>
                                    @if (isset($suppliers))
                                        @foreach ($suppliers as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $data->id == $model->supplier_id ? 'selected' : '' }}>
                                                {{ $data->code }}-{{ $data->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="purchase_date">Purchase Date</label>
                                <div class="input-group">
                                    <input type="text"
                                           class="form-control datepicker {{ $errors->has('purchase_date') ? ' is-invalid' : '' }}"
                                           name="purchase_date" id="purchase_date"
                                           value="{{ $model->purchase_date == null ? date('Y-m-d') : old('purchase_date', $model->purchase_date->format('Y-m-d')) }}"
                                           placeholder="" required="required">
                                    <?php date_default_timezone_set('Africa/Lagos'); ?>
                                    {{-- <input type="text"
                                        class="form-control datetimepicker input-sm {{ $errors->has('purchase_time') ? ' is-invalid' : '' }}"
                                        name="purchase_time" id="purchase_time"
                                        value="{{ $model->purchase_time == null ? date('h:i') : old('purchase_time', $model->purchase_time) }}"
                                        placeholder="Time" required="required">
                                    <div class="input-group-addon">
                                        <label for="purchase_date" class="fa fa-calendar">
                                        </label>
                                    </div> --}}
                                </div>
                                @if ($errors->has('purchase_date'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('purchase_date') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice">ATC/WayBill No</label>
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
                        </div>
                        <div class="col-md-4">
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
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                    <input type="hidden" name="updated_by" value="{{ Auth::id() }}" />
                    <div class="form-group text-right ">
                        <input type="submit" class="btn btn-primary" value="Save" />
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="card">
            <div class="card-header">
                Products
            </div>
            <div class="card-body">
                <form action="{{ route('ajax.cart.add') }}" method="POST" class="addCartItemForm">
                    <input type="hidden" name="purchase_id" value="{{ $model->id }}" />
                    <input type="hidden" name="type" value="{{ $type }}" />
                    @csrf
                    {{-- <div class="form-group">
                        <label for="category_id">Category</label>
                        <select type="number"
                            class="form-control select2-single ajax-categories  {{ $errors->has('category_id') ? ' is-invalid' : '' }}"
                            name="category_id" id="category_id" required="required"></select>
                        @if ($errors->has('category_id'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('category_id') }}</strong>
                            </div>
                        @endif
                    </div> --}}
                    <div class="form-group">
                        <label for="product_id">Product Name</label>
                        <select
                            class="form-control select2-single ajax-products {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                            name="product_id" id="product_id" required="required">
                            <option value="">Select...</option>
                            @if (isset($products))
                                {{-- @if (old('category_id', $model->category_id))
                                    @foreach (\App\Models\Product::where('category_id', old('category_id'))->get() as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                            {{ $data->name }}</option>
                                    @endforeach
                                @else --}}
                                @foreach ($products as $data)
                                    <option value="{{ $data->id }}"
                                        {{ $data->id == optional($model)->product_id ? 'selected' : '' }}>
                                        {{ $data->code }}-{{ $data->name }}</option>
                                @endforeach
                                {{-- @endif --}}
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="qty_supplied">Quantity</label>
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
                    <div class="form-group">
                        <label for="source_store_id">Store</label>
                        <select
                            class="form-control select2-single {{ $errors->has('source_store_id') ? ' is-invalid' : '' }}"
                            name="store_id" id="store_id" required="required">
                            @if (isset($stores))
                                <option value="">Select...</option>
                                @foreach ($stores as $data)
                                    <option value="{{ $data->id }}"
                                        {{ $data->id == $model->source_store_id ? 'selected' : '' }}>
                                        {{ $data->code }}-{{ $data->name }}</option>
                                @endforeach
                            @endif
                        </select>
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
                <div class="cart-container"></div>
            </div>
        </div>
    </div>
</div>


<div class="row table-responsive">

</div>
</div>
