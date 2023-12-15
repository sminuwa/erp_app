@isset($order)
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proformer Details: {{ optional($order)->customer->name }} | Invoice:
                    {{ optional($order)->invoice_no }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('sales_products.verify') }}" id="ledger_form">
                    <div class="display">

                    </div>
                    
                    <input type="hidden" name="print" value="print" />
                    <input type="hidden" name="modal" value="modal" />
                    <input type="hidden" name="order_id" value="{{$order?->id}}" />
                    <input type="hidden" name="invoice" value="{{$order?->invoice_no}}" />
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                            Close
                        </button>
                    </div>
                    @method('post')
                </form>
            </div>
        </div>
    </div>
@endisset
