@isset($order)
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Sale to User: {{ optional($order)->customer->name }} | Invoice:
                    {{ optional($order)->invoice_no }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('transfer.sale.to.user') }}" id="ledger_form">
                    @csrf
                    <h2 class="fa fa-check text text-success">Transfer Sale to User </h2>
                    <div class="display">

                    </div>
                    <div class="form-row">
                        <select name="user_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach (\App\Models\User::where('branch_id',\App\Models\User::userBranchAction())->get() as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="order_id" value="{{$order?->id}}" />
                    <input type="hidden" name="invoice" value="{{$order?->invoice_no}}" />
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal"><i class="fa fa-times"></i>
                            Close
                        </button>
                        
                        @can('verify.invoice')
                        <button type="submit" class="btn btn-success"><i class="fa fa-exchange"></i>
                            Transfer
                        </button>
                        @endcan
                    </div>
                    @method('post')
                </form>
            </div>
        </div>
    </div>
@endisset
