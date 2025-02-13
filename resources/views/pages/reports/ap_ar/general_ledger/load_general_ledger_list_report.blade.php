<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.general.ledger.list.report.print', [$company_id, $branch_id]) }}" target="_BLANK"
            class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="display table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h3 style="text-align: center;">{{ $branch->name ?? 'All Branches' }}</h3>
        <h5 style="text-align: center;">List of General Ledgers</h5>
    </caption>
    <thead>
        <tr>
            <th>Class </th>
            <th>Number </th>
            <th>Description </th>
            <th>Branch</th>
            <th>Is Control </th>
            <th>Status </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->class }} </td>
                <td> {{ $record->number }} </td>
                <td> {{ $record->description }} </td>
                <td> {{ $record->branch?->name }} </td>
                <td> {{ $record->is_control == 1 ? 'Yes' : 'No' }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Not active' }} </td>
            </tr>
        @endforeach
    </tbody>
</table>


