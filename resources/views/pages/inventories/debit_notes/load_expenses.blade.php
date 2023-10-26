<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-info"></i>
            Item Lists for Invoice
            @if (isset($purchase))
                {{ $purchase->reference }}
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
                        <th style="width:30%">Supplier Code</th>
                        <th style="width:30%">Supplier Name</th>
                        <th style="width:30%">Description</th>
                        <th>Amount</th>
                        <!--<th><span class="ion-refresh"></span></th> -->
                        {{-- <th><span class="ion-ios-trash"></span></th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart_products as $product)
                        <tr>
                            @php $supplier = App\Models\Supplier::find($product->attributes['supplier_id']) ;@endphp
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $supplier->code }}</td>
                            <td class="text-left">{{ $supplier->name }}</td>
                            <td class="text-left">{{ $product->name }}</td>

                            <form action="{{ route('debit.note.cart.update') }}" method="post" id="p{{ $product->id }}">
                                @csrf
                                @method('PUT')
                                <td>
                                    <input type="text" name="price" id="price{{ $product->id }}"
                                        class="form-control price" style="min-width:65px;"
                                        value="{{ $product->price }}"
                                        data-val="{{ $product->price }}"
                                        data-value="p{{ $product->id }}">
                                <input type="hidden" name="id" class="form-control" value="{{ $product->id }}">
                                <input type="hidden" name="supplier_id" class="form-control" value="{{ $supplier->id }}">
                                <input type="hidden" name="name" class="form-control" value="{{ $product->name }}">
                                
                                {{-- <td>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                </button>
                            </td> --}}
                            </form>

                            <td>
                                <button class="btn btn-danger btn-sm delete" type="button"
                                    data-val="{{ $product->id }}">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                                <form id="delete-form-{{ $product->id }}"
                                    action="{{ route('debit.note.cart.remove', $product->id) }}" method="post"
                                    style="display:none;">
                                    <input type="hidden" name="purchase" id="purchase" value="{{ $purchase->id }}" />
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div class="alert alert-success">
            Total : &#8358; <span id="total">{{ number_format(Cart::getTotal()) }}</span>
        </div>
        <form action="{{ route('suppliers.debit.note.store') }}" method="POST">
            @csrf
            <input type="hidden" name="purchase_id"  value="{{ $purchase->id }}" />
            <textarea name="comment" placeholder="Comment" rows="5" cols="100" class="form-control"></textarea>
            <div class="form-group text-right">
                <input type="submit" name="Submit" class=" btn btn-primary" value="Submit" />
            </div>
        </form>
    </div>
    <!-- /.card-body -->
</div>
