<table class="table table-bordered table-striped" id="record1">
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Invoice </th>
            <th>Purchase Date </th>
            <th>Purchase Mode </th>
            <th>Amount (&#8358;) </th>
            <th>Status </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td> {{ optional($record->supplier)->name }} </td>
                <td> {{ $record->invoice }} </td>
                <td> {{ $record->purchase_date->toDayDateTimeString() }} </td>
                <td> {{ $record->purchase_mode }} </td>
                <td style="text-align: right"> {{ number_format($record->totalProductCost()->total, 2) }} </td>
                <td> {{ $record->status == 1 ? 'Completed' : 'Pending' }} </td>
                <td>
                    <a class="btn btn-secondary btn-sm" href="{{ route('purchases.request.show', $record->id) }}">
                        <span class="fa fa-eye"></span>
                    </a>
                    @can('edit.item.purchase')
                        <a class="btn btn-secondary btn-sm" href="{{ route('purchases.request.edit', $record->id) }}">
                            <span class="fa fa-pencil"></span>
                        </a>
                    @endcan
                    <a class="btn btn-primary btn-sm" href="{{ route('purchase.request.print', $record->id) }}"
                        title="Print Invoice" target="_BLANK">
                        <span class="fa fa-print"></span>
                    </a>
                    @can('delete.item.purchase')
                        <form onsubmit="return confirm('Are you sure you want to cancel?')"
                            action="{{ route('purchases.request.destroy', $record->id) }}" method="post" style="display: inline">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-secondary btn-sm cursor-pointer">
                                <i class="text-danger fa fa-remove"></i>
                            </button>
                        </form>
                    @endcan
                    @if ($record->waybill_no != null)
                        <a class="btn btn-primary btn-sm" href="{{ route('purchase.request.waybill.print', $record->id) }}"
                            title="Print Invoice" target="_BLANK">
                            <span class="ion-printer"> Print Waybill</span>
                        </a>
                    @endif
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#customermodal{{ $loop->index + 1 }}"
                        class="btn btn-sm btn-primary float-md-right">WayBill</a>
                </td>
            </tr>
            <!--  modal create customer -->
            <div class="modal fade" id="customermodal{{ $loop->index + 1 }}" style="display: none;" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Generate Purchase Waybill with Invoice
                                ({{ $record->invoice }})
                                by {{ $record->supplier->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('purchase.request.generate.waybill', $record->id) }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Way Bill No: </label>
                                            <input type="text" class="form-control" name="waybill_no"
                                                value="{{ old('waybill_no', $record->invoice) }}"
                                                placeholder="WayBill No">
                                        </div>
                                        <div class="form-group">
                                            <label>Driver's Name:</label>
                                            <input type="text" class="form-control" name="driver_name"
                                                value="{{ old('driver_name', $record->driver_name) }}"
                                                placeholder="Driver name">
                                        </div>
                                        <div class="form-group">
                                            <label>Location ID: </label>
                                            <input type="text" class="form-control" name="location_id"
                                                value="{{ old('location_id', $record->location_id) }}"
                                                placeholder="Enter Address">
                                        </div>

                                        <div class="form-group">
                                            <label>Warehouse</label>
                                            <input type="text" class="form-control" name="warehouse"
                                                value="{{ old('warehouse', $record->warehouse) }}"
                                                placeholder="Warehouse">
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle Reg No.:</label>
                                            <input type="text" class="form-control" name="vehicle_reg_no"
                                                value="{{ old('vehicle_reg_no', $record->vehicle_reg_no) }}"
                                                placeholder="Vehicle Registration No">
                                        </div>
                                        <div class="form-group">
                                            <label>Transporter:</label>
                                            <input type="text" class="form-control" name="transporter"
                                                value="{{ old('transporter', $record->transporter) }}"
                                                placeholder="Transporter name">
                                        </div>
                                    </div>
                                    <input type="hidden" name="modal" value="modal" />
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark" data-dismiss="modal"><i
                                            class="fa fa-times"></i>
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-info px-3"><i class="icon-trash"></i>
                                        Generate
                                    </button>
                                </div>
                                @method('post')
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- End modal delete -->
        @endforeach
    </tbody>
</table>
