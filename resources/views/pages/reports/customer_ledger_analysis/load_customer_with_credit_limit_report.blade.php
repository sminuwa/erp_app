<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.credit_limit.report.print', [$company_id,$branch_id]) }}" target="_BLANK"
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
            <th style="width: 50%" colspan="3">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="2">Processed By {{ auth()->user()->name }}</th>
        </tr>
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
