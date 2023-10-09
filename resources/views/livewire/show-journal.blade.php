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
                    <p>Date: {{ $journal->date }}</p>
                </div>
                <div class="col-md-7 mb-3">
                    <p>Date: {{ $journal->description }}</p>
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
                                        <td>{{ $journal_item->debit }}</td>
                                        <td>{{ $journal_item->credit }}</td>
                                        <td>{{ $journal_item->description }}</td>
                                        <td>{{ $journal->status }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 mt-3">
                            <h4>
                                <small>Total Credit:</small> N{{ $total_credit }} <br>
                                <small>Total Debit:</small> N{{ $total_debit }} <br>
                                <small>Balance:</small> N{{ $total_credit-$total_debit }}
                            </h4>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit"  class="btn btn-success btn-sm">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select2-single').on('change', function (e) {
        var data = $('.select2-single').select2("val");
    @this.set('selected', data);
    });
    $('.datepicker').on('change', function (e) {
    @this.set('datepicker', e.target.value);
    });
</script>
