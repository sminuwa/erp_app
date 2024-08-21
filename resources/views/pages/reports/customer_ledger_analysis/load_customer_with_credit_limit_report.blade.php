<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.credit_limit.report.print', [$branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Customers with Credit Limit</h5>
    </caption>
    <thead>
        <tr>
            <th>ACCOUNT NO</th>
            <th>NAME</th>
            <th>TYPE</th>
            <th>CREDIT LIMIT</th>
            <th>BRANCH</th>
        </tr>
    </thead>
    @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer->code }}</td>
            <td>{{ strtoupper($customer->name) }}</td>
            <td>{{ $customer->type }}</td>
            <td style="text-align: right">{{ number_format($customer->credit_limit, 2) }}</td>
            <td>{{ $customer->branch->name  }}</td>
        </tr>
    @endforeach
</table>
