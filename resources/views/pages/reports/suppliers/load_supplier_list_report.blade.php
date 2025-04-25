
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of Suppliers</h5>
    </caption>
    <thead>
        <tr>
            <th style="width: 50%" colspan="4">Date Processed:
                {{ Carbon\Carbon::parse(date('Y-m-d H:i:s'))->format('l, jS F Y h:i A') }}
            </th>
            <th style="width: 50%;text-align:right" colspan="4">Processed By {{ auth()->user()->name }}</th>
        </tr>
        <tr>
            <th>ACCOUNT NO</th>
            <th>NAME</th>
            <th>PHONE</th>
            <th>EMAIL</th>
            <th>ADDRESS</th>
            <th>BRANCH</th>
            <th>ACCOUNT BALANCE</th>
            <th>DATE REGISTERED</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->code }}</td>
                <td>{{ strtoupper($supplier->name) }}</td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->email }}</td>
                <td>{{ $supplier->address }}</td>
                <td>{{ $supplier->branch->name ?? '' }}</td>
                <td style="text-align: right;">{{ number_format($supplier->balance ?? 0,2) }}</td>
                <td>{{ \Carbon\Carbon::parse($supplier->created_at)->toFormattedDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
