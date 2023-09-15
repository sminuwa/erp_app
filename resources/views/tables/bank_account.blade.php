<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Account Name </th>
            <th>Account No </th>
            <th>Branch</th>
            <th>Account Balance </th>
            <th>Account Type </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td> {{ $record->account_name }} </td>
                <td> {{ $record->account_no }} </td>
                <td> {{ $record->branch?->name }} </td>
                <td style="text-align: right"> {{ number_format($record->account_balance, 2) }} </td>
                <td> {{ $record->account_type }} </td>
                <td> {{ $record->status == 1 ? 'Active' : 'Inactive' }} </td>
                <td>
                    @can('create.bank.account')
                        <a class="btn btn-secondary btn-sm" href="{{ route('bank_accounts.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    @can('delete.bank.account')
                        <form onsubmit="return confirm('Are you sure you want to delete?')"
                            action="{{ route('bank_accounts.destroy', $record->id) }}" method="post" style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
