@isset($order)
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Invoice Details: {{ optional($order)->customer->name }} | Invoice:
                    {{ optional($order)->invoice_no }}</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('order.invoice.approve', $order->id) }}" id="ledger_form">
                    @csrf
                    @method('PUT')
                    <span class="fa fa-id-badge bold">
                        {{ $order->linked_order != null ? 'and linked with ' : '' }}
                        {{ $order->linked_order->reference ?? '' }}
                        <div class="display">

                        </div>

                        <input type="hidden" name="print" value="print" />
                        <input type="hidden" name="modal" value="modal" />
                        <input type="hidden" name="reference" value="{{ $order?->reference }}" />

                        <textarea name="comment" id="comment" class="form-control" rows="5" cols="50" placeholder="Comment">{{ $order->comment }}</textarea>

                        <select name="order_status" id="order_status" class="form-control">
                            @if ($order->order_status == 'Pending')
                                <option value="Pending" {{ $order->order_status == 'Completed' ? 'selected' : '' }}>Pending
                                </option>
                            @endif
                            @if ($order->order_status == 'Pending')
                                <option value="Closed" {{ $order->order_status == 'Closed' ? 'selected' : '' }}>Closed
                                </option>
                            @endif
                            @if ($order->order_status == 'Closed' || $order->order_status == 'Completed')
                                <option value="Completed" {{ $order->order_status == 'Completed' ? 'selected' : '' }}>
                                    Completed</option>
                            @endif
                        </select>
                        <div class="modal-footer">
                            @if ($order->order_status == 'Pending' || $order->order_status == 'Closed')
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                                    Submit
                                </button>
                            @endif
                            <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                                Close
                            </button>
                        </div>

                </form>
            </div>
        </div>
    </div>
@endisset
