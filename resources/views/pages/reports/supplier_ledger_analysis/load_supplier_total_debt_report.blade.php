<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.supplier.total.debt.report.print', [$from_date, $to_date, $supplier_id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Supplier Total Debt Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>CUSTOMER NAME</th>
            <th>DEBT</th>
        </tr>
    </thead>
    @php
        $total_due = 0;
    @endphp
    @foreach ($sales as $sale)
        <tr>
            <td>{{ $sale->supplier }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->debt, 2, '.', ',') }}</td>
        </tr>
        @php
            $total_due += $sale->debt;
            
        @endphp
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right">TOTAL</th>
            <th style="text-align: right">
                @if ($total_due < 0)
                    &#8358;({{ number_format(abs($total_due), 2) }})
                @else
                    &#8358;{{ number_format($total_due, 2) }}
                @endif
            </th>
        </tr>
    </tfoot>
</table>
