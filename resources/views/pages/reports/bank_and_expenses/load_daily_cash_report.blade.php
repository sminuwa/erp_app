<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.daily.report.print', [$from_date, $to_date]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="false">
    <thead>
        <tr>
            <th>DATE</th>
            <th>TOTAL SALES</th>
            <th>SALES DISC</th>
            <th>DEBTORS PAYMENT</th>
            <th>LOAN PAID</th>
            <th>EXPENSE</th>
            <th>FROM BANK</th>
            <th>TO BANK</th>
            <th>CASH PURCHASE</th>
            <th>LOAN COLLECT</th>
            <th>PAYMENT TO SUPPLIER</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    <tbody>
        @php echo $result @endphp
    </tbody>
</table>
