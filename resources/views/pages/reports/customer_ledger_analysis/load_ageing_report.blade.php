<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.ageing.report.print', [$from_date, $to_date, $customer_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Ageing Report
            @if ($from_date != 'all')
                From
                {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                AND
                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
            @endif
        </h5>
        <h5 style="text-align: center;">
            CUSTOMER NAME: {{ $customer_id == 'all' ? 'All' : \App\Models\Customer::find($customer_id)->name }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>PHONE</th>
            <th>NAME</th>
            <th>INVOICE</th>
            <th>TRN DATE</th>
            <th>AMOUNT</th>
            <th>NET DUE DATE</th>
            <th>NOT DUE</th>
            <th>0-30 DAYS</th>
            <th>DUE INVOICE</th>
        </tr>
    </thead>
    @php
        $total_30_days = 0;
        $total_not_due = 0;
        $total_amount = 0;
        $total_due_invoice = 0;
        $flag_payment = false;
        $flag_payment_amount = 0;
    @endphp
    @foreach ($sales as $sale)
        @php
            $ledger = \App\Models\CustomerLedger::find($sale->id);
            $dateNow = \Carbon\Carbon::now();
            $amount = 0;
            $due_invoice = 0;
            if ($sale->dr != 0) {
                $flag_payment = true;
                $flag_payment_amount = $sale->dr;
            }
            
            $total_amount += $sale->cr != 0 ? $sale->cr : $sale->dr;
            $total_not_due += $sale->cr != 0 ? $sale->cr : 0;
            if ($sale->cr != 0) {
                if (\Carbon\Carbon::parse($ledger->order?->due_date)->lt(\Carbon\Carbon::parse(date('Y-m-d')))) {
                    //It is due
                    $amount = $sale->cr != 0 ? $sale->cr : 0 - $sale->dr;
                    $due_invoice = $amount;
                    if ($flag_payment == true) {
                        //$amount = 0 - $sale->dr;
                        $due_invoice = abs($amount) - $flag_payment_amount;
                        $flag_payment = false;
                    }
                } else {
                    //It not is due
                    $due_invoice = 0;
                }
            } else {
                $amount = 0 - $sale->dr;
                $due_invoice = $amount;
            }
            $total_30_days += $amount;
            $total_due_invoice += $due_invoice;
        @endphp


        <tr>
            <td>{{ $sale->phone }}</td>
            <td>{{ $sale->name }}</td>
            <td>{{ $sale->receipt_no == null ? $sale->systemid : $sale->receipt_no }}</td>
            <td>{{ $sale->cr != 0 ? \Carbon\Carbon::parse($ledger->order?->order_date)->toFormattedDateString() : \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}
            </td>
            <td style="text-align: right">
                &#8358;{{ number_format($sale->cr != 0 ? $sale->cr : 0 - $sale->dr, 2, '.', ',') }}
            </td>
            <td>{{ $sale->cr != 0 ? \Carbon\Carbon::parse($ledger->order?->due_date)->toFormattedDateString() : \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}
            </td>
            <td style="text-align: right">&#8358;{{ number_format($sale->cr != 0 ? $sale->cr : 0, 2, '.', ',') }}</td>
            <td style="text-align: right">&#8358;{{ number_format($amount, 2, '.', ',') }}</td>
            <td style="text-align: right">&#8358;{{ number_format($due_invoice, 2, '.', ',') }}</td>
        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="4">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_amount, 2, '.', ',') }}</th>
            <th></th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_not_due, 2, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_30_days, 2, '.', ',') }}</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_due_invoice, 2, '.', ',') }}</th>
        </tr>
    </tfoot>
</table>
