<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.customer.list.report.print', [$company_id,$branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Customers</h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="5">Date Processed: {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>ACCOUNT NO</th>
            <th>NAME</th>
            <th>TYPE</th>
            <th>PHONE</th>
            <th>EMAIL</th>
            <th>ADDRESS</th>
            <th>RO</th>
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
            <td>{{ $customer->relationOfficer->name ?? '' }}</td>
            <td>{{ $customer->branch->name ?? '' }}</td>
            
            <td>{{ \Carbon\Carbon::parse($customer->created_at)->toFormattedDateString() }}</td>
        </tr>
    @endforeach
</table>
