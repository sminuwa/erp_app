<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.cash.flow.report', [$from_date, $to_date, $company_id,$branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">{{ strtoupper($branch->name ?? 'All Branches') }} <br>
            CASH FLOW BETWEEN {{ Carbon\Carbon::parse($from_date)->toFormattedDateString() }} and
            {{ Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
    </caption>
    <tbody>
        <tr>
            <th style="text-align: left">Total Cash Generated</th>
            <td style="text-align: right">{{ number_format($total_generated, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: left">Total Bank Transfer</th>
            <td style="text-align: right">{{ number_format($total_bank_transfer, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: left">Total Cash at Hand</th>
            <td style="text-align: right">{{ number_format($total_at_hand, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: left">Total Cash in Bank</th>
            <td style="text-align: right">{{ number_format($total_cash_in_bank, 2) }}</td>
        </tr>
        <tr>
            <th style="text-align: left">Total Amount Expended</th>
            <td style="text-align: right">{{ number_format($total_amount_expended, 2) }}</td>
        </tr>

    </tbody>
</table>
