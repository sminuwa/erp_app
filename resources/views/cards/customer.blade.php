<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary btn-sm" href="{{ route('customers.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <a class="btn btn-secondary btn-sm" href="{{ route('customers.index', $record->id) }}">
                        <span class="fa fa-list"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-block">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>Name</th>
                    <td>{{ $record->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $record->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $record->phone }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $record->address }}</td>
                </tr>
                <tr>
                    <th>Credit Limit</th>
                    <td>{{ number_format($record->credit_limit, 2) }}</td>
                </tr>
                <tr>
                    <th>Opening Balance</th>
                    <td>
                        @if ($record->opening_balance < 0)
                            ({{ number_format(abs($record->opening_balance), 2) }}) as @
                            {{ optional($record->updated_at)->toDayDateTimeString() }}
                        @else
                            {{ number_format(abs($record->opening_balance), 2) }} as @
                            {{ optional($record->updated_at)->toDayDateTimeString() }}
                        @endif

                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
