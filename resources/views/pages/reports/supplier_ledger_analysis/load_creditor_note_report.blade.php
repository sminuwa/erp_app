<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.credit.note.report.print', [$from_date, $to_date, $supplier_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">Creditor Note Report
            From
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            AND
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5 style="text-align: center;">
            SUPPLIER NAME: {{ $supplier_id =="all"?"All suppliers":\App\Models\Supplier::find($supplier_id)->name }}
        </h5>
    </caption>
    <thead>
        <tr>
            <th>DATE</th>
            <th>RECIEPT NO </th>
            <th>SUPPLIER NAME</th>
            <th>AMOUNT</th>
            <th>CHEQUE NO</th>
        </tr>
    </thead>
    @php
        $total_credit = 0;
    @endphp
    @foreach ($sales as $sale)
        @php
            $total_credit += $sale->dr;
        @endphp
        <tr>

            <td>{{ \Carbon\Carbon::parse($sale->date)->toFormattedDateString() }}</td>
            <td>{{ $sale->Ref }}</td>
            <td>{{ $sale->supplier->name }}</td>
            <td style="text-align: right">&#8358;{{ number_format($sale->dr, 2, '.', ',') }}</td>
            <td>{{ $sale->teller_no }}</td>

        </tr>
    @endforeach
    <tfoot>
        <tr>
            <th style="text-align: right" colspan="3">TOTAL</th>
            <th style="text-align: right">
                &#8358;{{ number_format($total_credit, 2, '.', ',') }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
