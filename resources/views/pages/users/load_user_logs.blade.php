<div class="row">
    <div class="offset-10">
        <a href="{{ route('user.activity.logs.print', [$from_date, $to_date, $user->id]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>
<table class="table table-bordered caption" id="example1" data-ordering="true">
    <caption style="caption-size:top">
        <h5 style="text-align: center;">User Activity Logs
            Between
            {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
            and
            {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
        </h5>
        <h5 style="text-align: center;">User Name:
            {{ $user->name }}</h5>
    </caption>
    <thead>
        <tr>
            <th>Action </th>
            <th>Role</th>
            <th>Dare/Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ $record->action }} </td>
                <td> {{ $record->roles }} </td>
                <td> {{ $record->created_at->toDayDateTimeString() }} </td>
            </tr>
        @endforeach
    </tbody>
</table>
