<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.supplier.payment.report.print', [$from_date, $to_date, $supplier_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Supplier Payment History Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="4">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>DATE</th>
            <th>SUPPLIER NAME</th>
            <th>BALANCE</th>
            <th>AMOUNT PAID</th>
            <th>TELLER NO</th>
            <th>PAY MODE</th>
            <th>ACC. NAME</th>
            <th>DATE POSTED</th>

        </tr>
    </thead>
    <tbody>
        @php
            $total_sold = 0;
            $total_pay = 0;
            $total_discount = 0;
            $total_due = 0;
            $balance = 0;
        @endphp
        @foreach ($sales as $sale)
            @php
                $sum_cr = \App\Models\SupplierLedger::where('id', '<', $sale->id)
                    ->where('supplier_id', $sale->supplier_id)
                    ->sum('cr');
                $sum_dr = \App\Models\SupplierLedger::where('id', '<', $sale->id)
                    ->where('supplier_id', $sale->supplier_id)
                    ->sum('dr');
                $balance = $sum_cr - $sum_dr;
                $total_pay += $sale->dr;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
                <td>{{ $sale->supplier }}</td>
                <td style="text-align: right">
                    @if ($balance < 0)
                        ({{ number_format(abs($balance), 2) }})
                    @else
                        {{ number_format($balance, 2) }}
                    @endif
                </td>
                <td style="text-align: right">{{ number_format($sale->dr, 2, '.', ',') }}</td>
                <td>{{ $sale->teller_no }}</td>
                <td>{{ $sale->payment_mode }}</td>
                <td>{{ $sale->account_name }}</td>
                <td>{{ \Carbon\Carbon::parse($sale->created_at)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2"></th>
            <th style="text-align: right">B/F:
                @if ($balance < 0)
                    ({{ number_format(abs($balance), 2) }})
                @else
                    {{ number_format($balance, 2) }}
                @endif
            </th>
            <th style="text-align: right">{{ number_format($total_pay, 2, '.', ',') }}</th>
            <th colspan="4"></th>
        </tr>
    </tfoot>
</table>
