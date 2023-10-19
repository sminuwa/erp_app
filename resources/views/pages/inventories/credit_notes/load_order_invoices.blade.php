@foreach ($orders as $order)
    <tr>
        <td><a href="javascript:void(0)" class="invoice" data-val="{{ $order->invoice_no }}">{{ $order->invoice_no }}</a>
        </td>
        <td>{{ $order->customer->name }}</td>
        <td>{{ Carbon\Carbon::parse($order->order_date)->toFormattedDateString() }}
        </td>
        <td style="text-align: right"><a href="javascript:void(0)" class="invoice" data-val="{{ $order->invoice_no }}"><span
                    class=""></span>Select</a></td>
    </tr>
@endforeach
<script>
    $('.invoice').on('click', function() {
        var invoice_no = $(this).attr('data-val');
        $('#load').html("<h3>Please wait... while it is loading...</h3>");
        $.ajax({
            url: "{{ route('load.order.cart') }}",
            method: 'GET',
            data: {
                invoice_no: invoice_no
            },
            success: function(response) {

                $('#load').html(response);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>
