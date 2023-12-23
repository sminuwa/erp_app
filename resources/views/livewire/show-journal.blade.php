<div>
    <div class="card">
        <div class="card-body">
            <div class="col-12">
                @if(session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>
            <div class="col-12 mb-4">
                <h5>Journal Details</h5>
            </div>
            <div class="row">
                <div class="col-md-5 mb-3">
                    <p>Reference: {{ $journal->reference }}</p>
                    <p>Date: {{ $journal->date }}</p>
                    <p>Description: {{ $journal->description }}</p>
                    <p>Created By: {{ $journal->createdBy->name ?? null }}</p>
                    <p>Posted By: {{ $journal->postedBy->name ?? null }}</p>
                    <p>Modified By: {{ $journal->updatedBy->name ?? null }}</p>
                </div>
                <div class="col-md-7 mb-3">

                </div>

                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-12">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $total_credit = $total_debit = 0; ?>
                                @foreach($journal->items as $journal_item)
                                    <?php $total_credit += $journal_item->credit; ?>
                                    <?php $total_debit += $journal_item->debit; ?>
                                    <tr :wire:key="{{ $loop->index }}">
                                        <td>
                                            {{ $journal_item->account()->code ?? $journal_item->account()->number }} -
                                            {{ $journal_item->account()->name ?? $journal_item->account()->description }}
                                        </td>
                                        <td>{{ currency_sign().$journal_item->debit }}</td>
                                        <td>{{ currency_sign().$journal_item->credit }}</td>
                                        <td>{{ $journal_item->description }}</td>
                                        <td>{!!  $journal->status == 0 ? '<span class="badge badge-danger">pending</span>' : '<span class="badge badge-success">posted</span>' !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 mt-3">
                            <h4>
                                <small>Total Credit:</small> {{ currency_sign().$total_credit }} <br>
                                <small>Total Debit:</small> {{ currency_sign().$total_debit }} <br>
                                <small>Balance:</small> {{ currency_sign().$total_credit-$total_debit }}
                            </h4>
                        </div>
                        <div class="col-md-12 mt-3">
                            <a href="{{ route('journal.print', $journal->id) }}" target="_blank" class="btn btn-dark btn-sm"><i class="fa fa-print"></i> Print</a>
                            @if($journal->status == 0)
                                <a href="{{ route('journal.post', $journal->id) }}"
                                   onclick="return confirm('Are you sure you want to post this journal?');"
                                   class="btn btn-primary btn-sm"><i class="fa fa-check"></i> Post</a>
                                <a href="{{ route('journal.edit', $journal->id) }}"  class="btn btn-success btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                <a href="{{ route('journal.delete', $journal->id) }}"
                                   onclick="return confirm('Are you sure you want to delete this journal?');"
                                   class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
                            @else
                                <a href="{{ route('journal.reverse', $journal->id) }}"
                                   onclick="return confirm('Are you sure you want reverse this transaction?')"
                                   class="btn btn-success btn-sm">Reverse</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

