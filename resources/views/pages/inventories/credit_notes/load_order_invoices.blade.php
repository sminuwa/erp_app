@foreach ($orders as $order)
    <tr>
        <td><a href="javascript:void(0)" class="invoice" data-val="{{ $order->reference }}">{{ $order->reference }}</a>
        </td>
        {{-- <td>{{ $order->customer->name }}</td> --}}
        <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
        </td>
        {{-- <td style="text-align: right"><a href="javascript:void(0)" class="invoice" data-val="{{ $order->invoice_no }}"><span
                    class=""></span>Select</a>
                </td> --}}
    </tr>
@endforeach
