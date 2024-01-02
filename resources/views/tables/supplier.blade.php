<table class="table table-bordered table-striped" id="record2">
    <thead>
        <tr>
            <th>SN</th>
            <th>Code </th>
            <th>Name </th>
            <th>Branch </th>
            <th>Phone </th>
            <th>Email </th>
            <th>Address </th>
            <th>Opening Balance </th>
            {{-- <th>Acc Num </th>
            <th>AccType </th>
            <th>Bank Name </th> --}}
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($records as $record)
            <tr>
                <td> {{ $loop->index + 1 }} </td>
                <td> {{ $record->code }} </td>
                <td> {{ $record->name }} </td>
                <td> {{ $record->branch?->name }} </td>
                <td> {{ $record->phone }} </td>
                <td> {{ $record->email }} </td>
                <td> {{ $record->address }} </td>
                <td> &#8358;{{ $record->opening_balance < 0 ? '(' . number_format(abs($record->opening_balance), 2, '.', ',') . ')' : number_format($record->opening_balance, 2, '.', ',') }}
                </td>
                {{-- <td> {{ $record->account_holder }} </td>
                <td> {{ $record->account_number }} </td>
                <td> {{ $record->account_type }} </td>
                <td> {{ $record->bank->name }} </td> --}}
                <td>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @can('suppliers.show')
                                <a class="dropdown-item" href="{{ route('suppliers.show', $record->id) }}">
                                    <span class="fa fa-eye"> View</span>
                                </a>
                            @endcan
                            @can('suppliers.edit')
                                <a class="dropdown-item" href="{{ route('suppliers.edit', $record->id) }}">
                                    <span class="fa fa-pencil"> Edit</span>
                                </a>
                            @endcan
                            @can('suppliers.destroy')
                                <form onsubmit="return confirm('Are you sure you want to delete?')"
                                    action="{{ route('suppliers.destroy', $record->id) }}" method="post"
                                    style="display: inline">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    <button type="submit" class="dropdown-item cursor-pointer btn-sm">
                                        <i class="text-danger fa fa-remove"> Delete</i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>

</table>
