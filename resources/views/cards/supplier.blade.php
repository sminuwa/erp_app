<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{ route('suppliers.show', $record->id) }}"> {{ $record->id }}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">
                    <a class="btn btn-secondary btn-sm" href="{{ route('suppliers.edit', $record->id) }}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                        action="{{ route('suppliers.destroy', $record->id) }}" method="post" style="display: inline">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                            <i class="text-danger fa fa-remove"></i>
                        </button>
                    </form>
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
                    <th>Phone</th>
                    <td>{{ $record->phone }}</td>
                </tr>
                <tr>
                    <th>Code</th>
                    <td>{{ $record->code }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $record->email }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $record->address }}</td>
                </tr>
                {{-- <tr>
                    <th>Account Holder</th>
                    <td>{{ $record->account_holder }}</td>
                </tr>
                <tr>
                    <th>Account Number</th>
                    <td>{{ $record->account_number }}</td>
                </tr>
                <tr>
                    <th>Account Type</th>
                    <td>{{ $record->account_type }}</td>
                </tr>
                <tr>
                    <th>Bank Name</th>
                    <td>{{ $record->bank->name }}</td>
                </tr> --}}
            </tbody>
        </table>
    </div>
</div>
