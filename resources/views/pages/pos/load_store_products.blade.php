<table class="table table-bordered table-striped text-left">
    <thead>
        <tr>
            <th>Store</th>
            <th>Name</th>
            <th>QTY Avail.</th>
            <th>Price</th>
            <th>Add To Cart</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Store</th>
            <th>Name</th>
            <th>QTY Avail.</th>
            <th>Price</th>
            <th>Add To Cart</th>
        </tr>
    </tfoot>
    <tbody>
        @foreach ($stores as $key => $store)
            <tr>
                <form action="{{ route('cart.store') }}" name="form{{$loop->iteration}}" id="form{{$loop->iteration}}" method="post" class="sale-form">
                   
                    <input type="text" name="_token" value="{{csrf_token() }}">
                    <input type="text" name="id" value="{{ $store->id }}">
                    <input type="text" name="name" value="{{ $store->name }}">
                    <input type="text" name="qty" value="1">
                    <input type="text" name="price"
                        value="{{ $store->selling_price }}">
                    <input type="text" name="cost_price"
                        value="{{ $store->cost_price }}">

                    <td>{{ $store->store }}</td>
                    <td>{{ $store->name }}</td>
                    <td align="center">{{ $store->qty_available }}</td>
                    <td align="right">
                        {{ number_format($store->selling_price) }}
                    </td>
                    @if ($store->qty_available > 0)
                        <td align="center">
                            <button type="submit" class="btn btn-sm btn-success px-2 cart-plus"  serial-data="{{$loop->iteration}}">
                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            </button>
                            <input type="submit" vale="S" class="btn btn-sm btn-primary"/>
                        </td>
                    @else
                        <td align="center">
                            <span class="fa fa-crosshairs text text-danger"></span>
                        </td>
                    @endif
                </form>
            </tr>
        @endforeach
    </tbody>

</table>