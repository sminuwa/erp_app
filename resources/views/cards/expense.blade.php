<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                <a href="{{ route('expenses.show', $record->id) }}"> {{ $record->item->name }}</a>
            </div>
            <div class="col-sm-3 text-right">
                <div class="btn-group">

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card-block">
                <table class="table table-bordered table-striped">
                    <tbody>
                        
                        <tr>
                            <th>Name</th>
                            <td>{{ $record->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>&#8358;{{ number_format($record->where('impress',$record->impress)->sum('amount'),2,'.',',') }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ \Carbon\Carbon::parse($record->date)->toDayDateTimeString() }}</td>
                        </tr>
                        <tr>
                            <th>Payment Mode</th>
                            <td>{{ $record->payment_mode }}</td>
                        </tr>
                        <tr>
                            <th>Impress</th>
                            <td>{{ $record->impress }}</td>
                        </tr>
                        <tr>
                            <th>Account Name</th>
                            <td>{{ $record->account->account_name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6 table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead>
                    <tr style="font-size: 14px;">
                        <th>S.N</th>
                        <th style="width:30%">Name</th>
                        <th style="width:15%">Amount</th>
                        <th style="width:30%">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart_expenses as $expense)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-left">{{ $expense->name }}</td>

                            <form action="{{ route('expenses.cart.update') }}" method="post">
                                @csrf
                                @method('PUT')
                                <td>
                                    <input type="number" name="amount" id="price{{ $loop->iteration }}" readonly
                                        class="form-control" style="min-width:65px;" value="{{ $expense->price }}">
                                </td>
                                <input type="hidden" name="id" class="form-control" value="{{ $expense->id }}">
                                <td>
                                    <input type="text" name="reason" class="form-control" readonly
                                        value="{{ $expense->attributes['reason'] }}">
                                </td>
                            </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="alert alert-success">
                Total : &#8358; {{ number_format($record->where('impress',$record->impress)->sum('amount'),2,'.',',') }}
            </div>
        </div>
    </div>
</div>
