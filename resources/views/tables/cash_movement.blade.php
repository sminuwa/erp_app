<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Date </th>
            <th>Amount </th>
            <th>From Account</th>
            <th>To Account</th>
            <th>Withdrawn By </th>
            <th>Deposited By </th>
            <th>Slip No </th>
            <th>Type </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                @if ($record->date_withdraw == null)
                    <td>{{ \Carbon\Carbon::parse($record->date_withdraw)->toFormattedDateString() }}
                    </td>
                @elseif ($record->date_deposit == null)
                    <td>{{ \Carbon\Carbon::parse($record->date_withdraw)->toFormattedDateString() }}
                    </td>
                @else
                    <td> {{ \Carbon\Carbon::parse($record->date_withdraw)->toFormattedDateString() }} </td>
                @endif
                <td style="text-align: right;font-weight:600"> &#8358;{{ number_format($record->amount, 2) }} </td>
                <td> {{ optional($record->fromAccount)->account_name }} </td>
                <td> {{ optional($record->toAccount)->account_name }} </td>
                <td> {{ optional($record->withdrawer)->name }} </td>
                <td> {{ optional($record->depositor)->name }} </td>
                <td> {{ $record->slip_no }} </td>
                <td> {{ $record->type == 'Both' ? 'Withdraw & Deposit' : $record->type }} </td>
                <td>
                    @can('view.deposit.withdraw')
                        <a class="btn btn-secondary btn-sm" href="{{ route('cash_movements.show', $record->id) }}">
                            <span class="fa fa-eye"></span>
                        </a>
                    @endcan
                    <a class="btn btn-secondary btn-sm" href="{{ route('cash_movements.print', $record->id) }}"
                        target="_BLANK">
                        <span class="fa fa-print"></span>
                    </a>
                    @if ($record->type == 'Deposit')
                        @can('edit.deposit.withdraw')
                            <a class="btn btn-secondary btn-sm" href="{{ route('deposits.edit', $record->id) }}">
                                <span class="fa fa-pencil"></span>
                            </a>
                        @endcan
                        @can('delete.deposit.withdraw')
                            <form onsubmit="return confirm('Are you sure you want to delete?')"
                                action="{{ route('deposits.destroy', $record->id) }}" method="post"
                                style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i>
                                </button>
                            </form>
                        @endcan
                    @elseif ($record->type == 'Both')
                        @can('edit.deposit.withdraw')
                            <a class="btn btn-secondary btn-sm" href="{{ route('cash_movements.edit', $record->id) }}">
                                <span class="fa fa-pencil"></span>
                            </a>
                        @endcan
                        @can('delete.deposit.withdraw')
                            <form onsubmit="return confirm('Are you sure you want to delete?')"
                                action="{{ route('cash_movements.destroy', $record->id) }}" method="post"
                                style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i>
                                </button>
                            </form>
                        @endcan
                    @elseif ($record->type == 'Withdraw')
                        @can('edit.deposit.withdraw')
                            <a class="btn btn-secondary btn-sm" href="{{ route('withdraw.edit', $record->id) }}">
                                <span class="fa fa-pencil"></span>
                            </a>
                        @endcan
                        @can('delete.deposit.withdraw')
                            <form onsubmit="return confirm('Are you sure you want to delete?')"
                                action="{{ route('withdraw.destroy', $record->id) }}" method="post"
                                style="display: inline">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                    <i class="text-danger fa fa-remove"></i>
                                </button>
                            </form>
                        @endcan
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
