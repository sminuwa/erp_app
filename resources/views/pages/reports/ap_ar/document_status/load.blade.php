<div class="row">
    <div class="offset-10">
        <a href="{{ route('ajax.print.remittance.report', [$from_date, $to_date, $branch->id, $status, $type]) }}"
            target="_BLANK" class="btn-success btn btn-sm">Print</a>
    </div>
</div>

@if ($type == 'Payment' || $type == 'Receipt')
    <table id="example1" class="table table-bordered table-striped text-left table-responsive-xl">
        <caption style="caption-size:top">
            <h5 style="text-align: center;">{{ strtoupper($branch->name) }} <br>
                AP-AR STATUS BETWEEN {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                AND
                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
            </h5>
        </caption>
        <thead>
            <tr>
                <th>Date</th>
                <th>Payment No</th>
                <th>Payee</th>
                <th>Account</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Status</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Date</th>
                <th>Payment No</th>
                <th>Payee</th>
                <th>Account</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Status</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($payments as $payment)
                <tr>

                    <td>{{ Carbon\Carbon::parse($payment->date)->toFormattedDateString() }}
                    </td>
                    <td>{{ $payment->receipt_no }}</td>
                    <td>
                        {{ $payment->payer()->code ? $payment->payer()->code . ' - ' . $payment->payer()->name : $payment->payer()->number . ' - ' . $payment->payer()->description }}
                    </td>
                    <td>
                        {{ $payment->account()->code ? $payment->account()->code . ' - ' . $payment->account()->name : $payment->account()->number . ' - ' . $payment->account()->description }}
                    </td>

                    <td align="right">{{ number_format($payment->amount, 2, '.', ',') }}</td>
                    <td>{{ $payment->description }}</td>
                    <td>{{ optional($payment->createdBy)->name }}</td>
                    <td class="@if ($payment->status == 0) bg-warning @endif">
                        {{ $payment->status == 1 ? 'Posted' : 'Pending' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'Interbank')
    <table id="example1" class="table table-bordered table-striped text-left table-responsive-xl">
        <caption style="caption-size:top">
            <h5 style="text-align: center;">{{ strtoupper($branch->name) }} <br>
                AP-AR STATUS BETWEEN {{ \Carbon\Carbon::parse($from_date)->toFormattedDateString() }}
                AND
                {{ \Carbon\Carbon::parse($to_date)->toFormattedDateString() }}
            </h5>
        </caption>
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt No</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Date</th>
                <th>Receipt No</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Created By</th>
                <th>Status</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($payments as $interbank)
                <tr class="@if ($interbank->status == 0) bg-warning @endif">

                    <td>{{ Carbon\Carbon::parse($interbank->date)->toFormattedDateString() }}
                    </td>
                    <td>{{ $interbank->reference }}</td>
                    <td align="right">{{ number_format($interbank->amount, 2, '.', ',') }}</td>
                    <td>{{ $interbank->description }}</td>
                    <td>{{ $interbank->source->description }}</td>
                    <td>{{ $interbank->destination->description }}</td>
                    <td>{{ optional($interbank->createdBy)->name }}</td>
                    <td class="@if ($interbank->status == 0) bg-warning @endif">
                        {{ $interbank->status == 1 ? 'Posted' : 'Pending' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@if ($type == 'Journal')
    <table id="example1" class="table table-bordered table-striped text-left table-responsive-xl">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Status</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Description</th>
                <th>Created By</th>
                <th>Status</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($payments as $record)
                <tr>
                    <td>{{ Carbon\Carbon::parse($record->date)->toFormattedDateString() }}</td>
                    <td>{{ $record->reference }}</td>
                    <td>{{ $record->description ?? null }}</td>
                    <td>{{ optional($record->createdBy)->name }}</td>
                    <td>
                        {{ $record->status == 1 ? 'Posted' : 'Pending' }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
@endif
