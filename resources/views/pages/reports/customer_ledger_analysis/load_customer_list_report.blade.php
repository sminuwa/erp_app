<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.list.report.print', [$branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="false">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Customers</h5>
    </caption>
    <thead>
        <tr>
            <th>ACCOUNT NO</th>
            <th>NAME</th>
            <th>TYPE</th>
            <th>PHONE</th>
            <th>EMAIL</th>
            <th>ADDRESS</th>
            <th>BRANCH</th>
            <th>DATE REGISTERED</th>
        </tr>
    </thead>
    @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer->code }}</td>
            <td>{{ strtoupper($customer->name) }}</td>
            <td>{{ $customer->type }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->address }}</td>
            <td>{{ $customer->branch->name ?? '' }}</td>
            <td>{{ \Carbon\Carbon::parse($customer->created_at)->toFormattedDateString() }}</td>
        </tr>
    @endforeach
</table>
