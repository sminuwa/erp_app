<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Sub Total</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">{{ $item->product->name }}</td>

                <form class="itemForm{{ $item->id }}" action="{{ route('inventories.purchases.ajax.create') }}" method="post" id="p{{ $item->id }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="item_id" value="{{ $item->id }}" name="item_id">
                    <td>
                        <input item_id="{{ $item->id }}" type="text" name="cost_price" id="price{{ $item->id }}"
                               class="form-control price" style="min-width:65px;"
                               onchange="validate(this.value,this.getAttribute('data-val'),this.getAttribute('id'))"
                               value="{{ $item->unit_price }}" data-val="{{ $item->unit_price }}"
                               data-value="p{{ $item->id }}">
                        <input type="hidden" name="expire_date" class="form-control"
                               value="{{ $item->quantity }}">
                    </td>
                    <td>
                        <input item_id="{{ $item->id }}" type="text" name="quantity" id="quantity{{ $item->id }}"
                               class="form-control quantity" data-value="p{{ $item->id }}"
                               style="min-width:58px;" value="{{ $item->quantity }}"
                               min="1" required>
                    </td>
                    <td>
                        <span item_id="{{ $item->id }}" name="subtotal" class="subtotal{{ $item->id }}">{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                    </td>
                    <input type="hidden" name="id" class="form-control" value="{{ $item->id }}">

                </form>

            </tr>
        @endforeach
    </tbody>
</table>
