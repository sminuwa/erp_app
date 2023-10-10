<table class="table table-bordered" id="record1">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php $total_expense = 0; @endphp
        @foreach ($purchase_expenses as $expense)
            <tr>
                @php $total_expense +=$expense->amount; @endphp
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $expense->name }}</td>
                <td style="text-align: right;">{{ number_format($expense->amount, 2) }}</td>
                <td style="text-align: right">
                    @if ($expense->purchase->status == 0)
                        <button class="btn btn-danger btn-sm" type="button" onclick="deleteItem({{ $expense->id }})">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <form id="delete-form-{{ $expense->id }}" action="{{ route('delete.purchase.expense', $expense->id) }}"
                            method="post" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    <tfoot>
        <tr>
            <td>Total:</td>
            <td colspan="2">{{ number_format($total_expense, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    </tbody>
</table>
