@foreach ($invoices as $invoice)
    <tr>
        <td>{{ $invoice->created_at }}</td>
        <td>{{ $invoice->purchase->reference }}</td>
        <td>{{ $invoice->supplier->name }}</td>
        <td>{{ $invoice->amount }}</td>
        <td style="text-align: right">
            <a href="javascript:void(0)" class="invoice" data-val="{{ $invoice->id }}"><span class=""></span>Delete</a>
        </td>
    </tr>
@endforeach
