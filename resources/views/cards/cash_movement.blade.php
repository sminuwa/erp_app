<div class="card card-default">
    <div class="card-header">
        <div class="row">

            <div class="col-sm-3 text-right">
                <div class="btn-group">

                    <a href="{{ route('deposits.create') }}" class="btn btn-sm btn-secondary"
                        style="margin-left: 2px;"><span class="ion-jet"> </span> New Deposit</a>
                    <a href="{{ route('withdraw.create') }}" class="btn btn-sm btn-secondary"
                        style="margin-left: 2px;"><span class="ion-jet"> </span> New Withdraw</a>
                    <a href="{{ route('cash_movements.create') }}" class="btn btn-sm btn-secondary"
                        style="margin-left: 2px;"><span class="ion-model-s"> </span>Withdraw & Deposit</a>
                    <div class="container-fluid">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>Amount</th>
                        <td>&#8358;{{ number_format($record->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>
                            {{ $record->type == 'Both' ? 'Withdraw ' : $record->type }} Account
                        </th>
                        <td class="text text-info" style="font-weight: 600">
                            {{ optional($record->fromAccount)->account_name }}
                            ({{ optional($record->fromAccount)->account_no }}),
                            {{ optional(optional(optional($record->fromAccount)->branch)->bank)->name }}<br />
                            Balance before
                            {{ $record->type == 'Both' ? 'withdraw & deposit' : strtolower($record->type) }}:
                            &#8358;{{ number_format($record->source_balance_before, 2) }}<br />
                            Current Balance:
                            &#8358;{{ number_format(optional($record->fromAccount)->account_balance, 2) }}
                        </td>
                    </tr>
                    @if ($record->type == 'Both')
                        <tr>
                            <th> Deposit Account</th>
                            <td class="text text-info" style="font-weight: 600">
                                {{ optional($record->toAccount)->account_name }}
                                ({{ optional($record->toAccount)->account_no }}),
                                {{ optional(optional(optional($record->toAccount)->branch)->bank)->name }}<br />
                                Balance before:
                                {{ $record->type == 'Both' ? 'withdraw & deposit' : strtolower($record->type) }}:
                                &#8358;{{ number_format($record->destination_balance_before, 2) }}<br />
                                Current Balance:
                                &#8358;{{ number_format(optional($record->toAccount)->account_balance, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Slip No</th>
                        <td>{{ $record->slip_no }}</td>
                    </tr>
                    @if ($record->type != 'Deposit')
                        <tr>
                            <th>Withdrawn By</th>
                            <td>{{ optional($record->withdrawer)->name }}</td>
                        </tr>
                    @endif
                    @if ($record->type != 'Withdraw')
                        <tr>
                            <th>Deposited By</th>
                            <td>{{ optional($record->depositor)->name }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Sent By</th>
                        <td>{{ optional($record->sender)->name }}</td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>{{ $record->type == 'Both' ? 'Withdraw & Deposit' : $record->type }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ \Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                    </tr>
                    <tr>
                        <th>Captured By</th>
                        <td>{{ optional($record->capturedBy)->name }}</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
