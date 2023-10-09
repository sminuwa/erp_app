<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <table id="example1" class="table table-bordered table-striped text-left table-responsive-xl">
        <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach ($records as $record)
            <tr>

                <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                <td>{{ $record->reference }}</td>
                <td align="right">{{ number_format($record->amount, 2, '.', ',') }}</td>
                <td>{{ $record->description ?? null }}</td>
                <td>{{ optional($record->createdBy)->name }}</td>
                <td align="center">
                    {{-- <a href="{{ route('interbank.print', $payment->id) }}"
                        target="_BLANK" class="btn btn-secondary btn-sm">
                        <i class="fa fa-print" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('interbank.print.pos', $payment->id) }}"
                        target="_BLANK" class="btn btn-secondary btn-sm">
                        <i class="fa fa-print" aria-hidden="true">PoS</i>
                    </a> --}}
                    <a href="javascript:void(0)" data-toggle="modal"
                       data-target="#payment_edit{{ $record->id }}"
                       data-val="{{ $record->id }}" class="btn btn-primary btn-sm edit">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>

                </td>
            </tr>

        @endforeach
        </tbody>

    </table>
</div>
